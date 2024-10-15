<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Scripts;

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
 * @package CloudForest\ApiClientPhp\Scripts
 */
class JsonSchema
{
    private string $namespace = 'CloudForest\\ApiClientPhp\\Schema\\';
    private PhpDocParser $phpDocParser;
    private Lexer $lexer;

    /**
     * Set up the doc block parser to extract @var tags just once. It can be
     * used throughout the recursive calls to £this->generate.
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
     * @param string $className
     * @return mixed[]
     */
    public function generate(string $className)
    {
        // Initial schema definition
        $schema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [],
        ];

        // Set up reflection to loop through class properties
        $className = $this->namespace . $className;
        if (!class_exists($className)) {
            throw new \Exception('Cannot find class ' . $className);
        }
        $reflector = new ReflectionClass($className);
        $properties = $reflector->getProperties();

        // Loop through the properties to add them to the schema. For each
        // property, get the doc block and extract the @var tags. Ensure there
        // is only one: it only makes sense to map one var tag to a JSON Schema
        // property. Then work out what type is in the £var tag and handle it
        // accordingly.
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

        // Done!
        return $schema;
    }

    /**
     * handleGenericTypeNode
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
        // Limitation 1: Only support array types with generics
        if ($typeName !== 'array') {
            throw new \Exception('Cannot handle that type yet: ' . $typeName);
        }

        // Limitation2: Only support a single generic.
        if (count($type->genericTypes) > 1) {
            throw new \Exception('Cannot handle more than one generic' . $typeName);
        }
        $generic = $type->genericTypes[0];

        // EG1: Array shape generics
        if ($generic instanceof ArrayShapeNode) {
            $schema = $this->handleType($generic);
        } elseif ($generic instanceof IdentifierTypeNode) {
            // EG2: A list of children from our schema
            if (class_exists($this->namespace . $generic->name)) {
                $schema = [
                    'type' => 'array',
                    'items' => $this->generate($generic->name),
                ];
            }

            // EG3: A list of built-ins
            else {
                $schema = [
                    'type' => 'array',
                    'items' => $this->handleType($generic),
                ];
            }
        }
        return $schema;
    }

    /**
     * handleUnionTypeNode
     * Split up a compound aka union type and handle each one seperately. For
     * example:
     * string|null
     *
     * @param UnionTypeNode $type
     * @return array<mixed>
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
     * Handle an individual type extracted from an var docblock tag and format
     * it as a JSON Schema spec. It returns an array with at least a type key
     * for use documenting a type for a property in the JSON schema:
     *
     * ["type" => "something"]
     *
     * and where possible it augments the spec with other info, like maxItems.
     *
     * @param TypeNode $type
     * @return array<mixed>
     */
    private function handleType($type)
    {
        $schema = [];
        // Where the type is an array shape, eg for coordinates:
        // @var array{float,float}
        if ($type instanceof ArrayShapeNode) {
            if (count($type->items) > 0) {
                $valueType = $type->items[0]->valueType;
                // Get the type in the shape, assuming:
                // 1. Everything is the same (ie, the php array<string,float>
                //    will yield a json schema of just {items: 'string'})
                // 2. The type has a name
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
        }

        // Otherwise using the type name...
        elseif ($type instanceof IdentifierTypeNode) {
            // Where the type is one of our schema classes, eg:
            // @var GeojsonSchema
            if (class_exists($this->namespace . $type->name)) {
                $schema = $this->generate($type->name);
            }

            // Where the type is one of our schema enums, eg:
            // @var CompartmentTypeEnum
            elseif (enum_exists($this->namespace . 'Enum\\' . $type->name)) {
                $schema = [
                    'type' => 'string',
                    'enum' => [],
                ];
                $enumName = $this->namespace . 'Enum\\' . $type->name;
                $refEnum = new ReflectionEnum($enumName);
                foreach ($refEnum->getCases() as $case) {
                    $t['enum'][] = $case->name;
                }
            }

            // Else handle simple types like string, float, etc
            else {
                $schema = ['type' => $this->castType($type->name)];
            }
        } else {
            throw new \Exception('Could not handle type node: ' . json_encode($type));
        }

        return $schema;
    }

    /**
     * Cast php types to a JSON schema equivalent.
     * @param string $type
     * @return string
     */
    private function castType(string $type)
    {
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
