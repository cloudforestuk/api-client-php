<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Scripts;

use ReflectionClass;
use ReflectionEnum;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;

/**
 * Print out a JSON Schema of the Inventory specification, starting at the top
 * with the CompartmentSchema class.
 *
 * @package CloudForest\ApiClientPhp\Scripts
 */
class JsonSchema
{
    private string $namespace = 'CloudForest\\ApiClientPhp\\Schema\\';

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

        // Set up doc block parser to extract @var tags
        $lexer = new Lexer(false);
        $constExprParser = new ConstExprParser();
        $typeParser = new TypeParser($constExprParser);
        $phpDocParser = new PhpDocParser($typeParser, $constExprParser);

        // Loop through the properties and add them to the schema.
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Get the @var from the doc block for the property
            $docBlock = $property->getDocComment();
            if (!$docBlock) {
                throw new \Exception('Failed to find doc block for ' . $propertyName);
            }
            $tokens = new TokenIterator($lexer->tokenize($docBlock));
            $phpDocNode = $phpDocParser->parse($tokens);
            $vars = $phpDocNode->getVarTagValues();

            // Make sure there's only one. It only makes sense to map 1 @var to
            // a JSON Schema property.
            if (count($vars) > 1) {
                throw new \Exception('Only 1 @var per property is supported');
            }
            $var = $vars[0];

            // Split up a type using generics and handle it.
            // EG1: An array with an array shape generic, used for coordinates:
            // array<array{float,float}>
            // EG2: A list of children:
            // @var Array<Subcompartment>
            // EG3: An array of a built-in type
            // @var Array<string>
            if ($var->type instanceof GenericTypeNode) {
                $typeName = $var->type->type->name;
                if ($typeName === 'array') {
                    $generic = $var->type->genericTypes[0];

                    // Array shape generics
                    if ($generic instanceof ArrayShapeNode) {
                        $schema['properties'][$propertyName] = $this->handleType($generic);
                    }

                    // A list of children
                    elseif ($generic instanceof IdentifierTypeNode) {
                        if (class_exists($this->namespace . $generic->name)) {
                            $schema['properties'][$propertyName] = [
                                'type' => 'array',
                                'items' => $this->generate($generic->name),
                            ];
                        }

                        // A list of built-ins
                        else {
                            $schema['properties'][$propertyName] = [
                                'type' => 'array',
                                'items' => $this->handleType($generic),
                            ];
                        }
                    }
                }
            }

            // Split up a compound type and handle each one seperately. For example:
            // @var string|null
            elseif ($var->type instanceof UnionTypeNode) {
                $schema['properties'][$propertyName] = ['anyOf' => []];
                foreach ($var->type->types as $type) {
                    $schema['properties'][$propertyName]['anyOf'][] = $this->handleType($type);
                }
            }

            // Handle everything else. Examples:
            // @var CompartmentTypeEnum
            // @var string
            // @var array{float,float}
            else {
                $schema['properties'][$propertyName] = $this->handleType($var->type);
            }
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
        // Where the type is an array shape, eg for coordinates:
        // @var array{float,float}
        if ($type instanceof ArrayShapeNode) {
            if (count($type->items) > 0) {
                $valueType = $type->items[0]->valueType;
                // Get the type in the shape, assuming:
                // 1. Everything is the same (ie, the php array<string,float> wil; yield a json schema of just items: 'string')
                // 2. The type has a name, otherwise bail to 'string'
                $valueTypeName = property_exists($valueType, 'name') ? $valueType->name : 'string';
                return [
                    'type' => 'array',
                    'items' => ['type' => $this->castType($valueTypeName)],
                    'maxItems' => count($type->items),
                ];
            } else {
                return ['type' => 'array'];
            }
        }

        // Otherwise using the type name...
        elseif ($type instanceof IdentifierTypeNode) {
            // Where the type is one of our schema classes, eg:
            // @var GeojsonSchema
            if (class_exists($this->namespace . $type->name)) {
                return $this->generate($type->name);
            }

            // Where the type is one of our schema enums, eg:
            // @var CompartmentTypeEnum
            elseif (enum_exists($this->namespace . 'Enum\\' . $type->name)) {
                $t = [
                    'type' => 'string',
                    'enum' => [],
                ];
                $enumName = $this->namespace . 'Enum\\' . $type->name;
                $refEnum = new ReflectionEnum($enumName);
                foreach ($refEnum->getCases() as $case) {
                    $t['enum'][] = $case->name;
                }
                return $t;
            }

            // For everything else, eg:
            // @var string
            return ['type' => $this->castType($type->name)];
        }

        // Else bail out and return a string type...
        return ['type' => 'string'];
    }

    /**
     * Cast php types to a JSCO schema equivalent
     * @param string $type
     * @return string
     */
    private function castType(string $type) {
        if ($type === 'float') {
            return 'number';
        } else {
            return $type;
        }
    }
}
