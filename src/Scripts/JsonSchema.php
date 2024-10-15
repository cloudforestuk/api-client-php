<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Scripts;

use Exception;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use ReflectionClass;
use ReflectionEnum;

/**
 * Print out a JSON Schema of the Inventory specification, starting at the top
 * with the CompartmentSchema class.
 *
 * It by no means supports all PHPDoc type annotations, but hopefully it
 * will always throw an exception in these cases rather than coming up with an
 * incorrect JSON schema. We can then work out whether to change the PHPDoc or
 * upgrade this to support it.
 *
 * It uses PHPStan's PHPDoc parser, so it therefore supports phpstan's take on
 * PHPDoc types: https://phpstan.org/writing-php-code/phpdoc-types
 *
 * @package CloudForest\ApiClientPhp\Scripts
 */
class JsonSchema
{
    private string $namespace = 'CloudForest\\ApiClientPhp\\Schema\\';
    private PhpDocParser $phpDocParser;
    private Lexer $lexer;

    /**
     * Set up the doc block parser to extract var tags. It can be then be reused
     * throughout the recursive calls to $this->generate.
     * @return void
     */
    public function __construct()
    {
        $this->lexer = new Lexer(false);
        $constExprParser = new ConstExprParser();
        $typeParser = new TypeParser($constExprParser);
        $this->phpDocParser = new PhpDocParser($typeParser, $constExprParser);
    }

    /**
     * Static method to run the generator.
     * @see composer.json
     * @return void
     * @throws \Exception
     */
    public static function run(): void
    {
        $generator = new JsonSchema();
        $schema = $generator->generate('CompartmentSchema');
        echo json_encode($schema, JSON_PRETTY_PRINT);
    }

    /**
     * Generate a JsonSchema for the class specified in $className.
     *
     * Use reflection to get a list of the class properties.
     *
     * Then loop through the properties to add them to the schema. For each
     * property, get the doc block and extract the var tags. Ensure there
     * is only one: it only makes sense to map one var tag to a JSON Schema
     * property. Then work out what type is in the var tag and handle it
     * accordingly.
     *
     * @param string $className
     * @return mixed[]
     * @throws \Exception
     */
    public function generate(string $className)
    {
        $schema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [],
        ];

        $className = $this->namespace . $className;
        if (!class_exists($className)) {
            throw new \Exception('Cannot find class ' . $className);
        }
        $reflector = new ReflectionClass($className);
        $properties = $reflector->getProperties();


        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $docBlock = $property->getDocComment();
            if (!$docBlock) {
                throw new \Exception('Failed to find doc block for ' . $propertyName);
            }
            $tokens = new TokenIterator($this->lexer->tokenize($docBlock));
            $phpDocNode = $this->phpDocParser->parse($tokens);
            $vars = $phpDocNode->getVarTagValues();

            if (count($vars) > 1) {
                throw new \Exception('Only one @var per property is supported');
            }
            $var = $vars[0];

            if ($var->type instanceof GenericTypeNode) {
                $schema['properties'][$propertyName] = $this->handleGenericTypeNode($var->type);
            } elseif ($var->type instanceof UnionTypeNode) {
                $schema['properties'][$propertyName] = $this->handleUnionTypeNode($var->type);
            } else {
                $schema['properties'][$propertyName] = $this->handleType($var->type);
            }
        }

        return $schema;
    }

    /**
     * Split up a type using generics and handle it.
     *
     * Limitation 1: We only handle array generics at the moment, ie array<T>.
     *
     * Limitation 2: The GenericTypeNode has two members, type and genericTypes,
     * which allows it to support multiple generics like SomethingClever<T1, T2>
     * Currently we only support a single generic.
     *
     * EG1: An array with an array shape generic, used for coordinates:
     * array<array{float,float}>
     *
     * EG2: A list of children from our schema:
     * [Array<Subcompartment>
     *
     * EG3: A list of a built-in type:
     * Array<string>
     *
     * @param GenericTypeNode $type
     * @return array<mixed>
     * @throws \Exception
     */
    private function handleGenericTypeNode(GenericTypeNode $type)
    {
        $schema = [];
        $typeName = $type->type->name;

        if ($typeName !== 'array') {
            throw new \Exception('Cannot handle that type yet: ' . $typeName);
        }

        if (count($type->genericTypes) > 1) {
            throw new \Exception('Cannot handle more than one generic' . $typeName);
        }

        $generic = $type->genericTypes[0];

        if ($generic instanceof ArrayShapeNode) {
            $schema = $this->handleType($generic);
        } elseif ($generic instanceof IdentifierTypeNode) {
            if (class_exists($this->namespace . $generic->name)) {
                $schema = [
                    'type' => 'array',
                    'items' => $this->generate($generic->name),
                ];
            } else {
                $schema = [
                    'type' => 'array',
                    'items' => $this->handleType($generic),
                ];
            }
        }
        return $schema;
    }

    /**
     * Split up a compound aka union type and handle each one seperately. For
     * example:
     * string|null
     *
     * @param UnionTypeNode $type
     * @return array<mixed>
     * @throws \Exception
     */
    private function handleUnionTypeNode(UnionTypeNode $type)
    {
        $schema = ['anyOf' => []];
        foreach ($type->types as $type) {
            $schema['anyOf'][] = $this->handleType($type);
        }
        return $schema;
    }

    /**
     * Handle a type node extracted from a var docblock tag and format
     * it as a JSON Schema. This is an array with at least a type key
     * for the JSON Schema:
     *
     * ["type" => "something"]
     *
     * and where possible it augments the schema with other info, like maxItems,
     * enums or array items.
     *
     * It splits the processing into two cases:
     *
     * Case 1: array shape types as used for coordinates:
     * array{float,float}
     *
     * Case 2 identifier types where the type has a name for us to lookup.
     *
     * @param TypeNode $type
     * @return array<mixed>
     * @throws \Exception
     */
    private function handleType($type)
    {
        $schema = [];
        if ($type instanceof ArrayShapeNode) {
            $schema = $this->handleArrayShapeNode($type);
        } elseif ($type instanceof IdentifierTypeNode) {
            $schema = $this->handleIdentifierTypeNode($type);
        } else {
            throw new \Exception('Could not handle type node: ' . json_encode($type));
        }
        return $schema;
    }

    /**
     * Handle identifier type nodes. There are 3 examples that are handled here:
     *
     * EG1: types using one of our schema classes
     * EG2: types using one of our schema enums
     * EG3: all other tpyes, intended to support built-in types
     *
     * @param IdentifierTypeNode $type
     * @return array<mixed>
     * @throws \Exception
     */
    private function handleIdentifierTypeNode(IdentifierTypeNode $type)
    {
        $schema = [];
        if (class_exists($this->namespace . $type->name)) {
            $schema = $this->generate($type->name);
        } elseif (enum_exists($this->namespace . 'Enum\\' . $type->name)) {
            $schema = [
                'type' => 'string',
                'enum' => [],
            ];
            $enumName = $this->namespace . 'Enum\\' . $type->name;
            $refEnum = new ReflectionEnum($enumName);
            foreach ($refEnum->getCases() as $case) {
                $t['enum'][] = $case->name;
            }
        } else {
            $schema = ['type' => $this->castType($type->name)];
        }
        return $schema;
    }

    /**
     * Handle an array shape type, for example as used for the coordinates:
     *
     * `array{float,float}` becomes `{type: 'array', 'items: {type: 'number'}}`
     *
     * A limitation of this is it assumed the first type in the shape is the
     * same throughout. It cannot yet handle `array{float,int}` for example.
     *
     * @param ArrayShapeNode $type
     * @return array<mixed>
     * @throws \Exception
     */
    private function handleArrayShapeNode(ArrayShapeNode $type)
    {
        $schema = [];
        if (count($type->items) > 0) {
            $valueType = $type->items[0]->valueType;
            if (!property_exists($valueType, 'name')) {
                throw new \Exception('Value type does not have a name: ' . json_encode($valueType));
            }
            $schema = [
                'type' => 'array',
                'items' => ['type' => $this->castType($valueType->name)],
                'maxItems' => count($type->items),
            ];
        } else {
            $schema = ['type' => 'array'];
        }
        return $schema;
    }

    /**
     * Cast php types to a JSON schema equivalent.
     * @param string $type
     * @return string
     * @throws \Exception
     */
    private function castType(string $type)
    {
        $cast = '';
        switch ($type) {
            case 'null':
                $cast = 'null';
                break;
            case 'bool':
                $cast = 'boolean';
                break;
            case 'int':
                $cast = 'integer';
                break;
            case 'float':
                $cast = 'number';
                break;
            case 'string':
                $cast = 'string';
                break;
            case 'array':
                $cast = 'array';
                break;
            case 'object':
                $cast = 'object';
                break;
            default:
                throw new \Exception('Could not cast type: ' . $type);
        }

        return $cast;
    }
}
