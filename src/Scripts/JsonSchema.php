<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Scripts;

use CloudForest\ApiClientPhp\Schema\CompartmentSchema;

use ReflectionClass;
use ReflectionNamedType;

use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\ParserConfig;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * Print out a JSON Schema of the Inventory specification, starting at the top
 * with the CompartmentSchema class.
 *
 * This script has some known issues:
 *
 * 1. Cannot yet handle PhpDoc types of Array<T>. It does work with T[]
 * 2. Cannot handle compound types. From the docblock the annotation
 *   `string|null`or from PHP the type `?string` should become the JSON
 *   `["string", "null"]`
 *
 * @package CloudForest\ApiClientPhp\Scripts
 */
class JsonSchema
{
    /**
     * Run the script.
     * @see composer.json
     * @return void
     */
    public static function run(): void
    {
        $schema = JsonSchema::generateJsonSchema(CompartmentSchema::class);
        echo json_encode($schema, JSON_PRETTY_PRINT);
    }

    /**
     * Generate a JsonSchema for the class specified in $className.
     *
     * @param string $className
     * @return mixed[]
     */
    public static function generateJsonSchema(string $className)
    {
        // Look for classes in global namespace and in the Schema namespace
        if (!class_exists($className)) {
            $className = 'CloudForest\\ApiClientPhp\\Schema\\' . $className;
            if (!class_exists(($className))) {
                throw new \Exception('Class not found: ' . $className);
            }
        }
        $reflector = new ReflectionClass($className);
        $properties = $reflector->getProperties();

        $schema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [],
        ];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            echo "---------\nProperty name: " . $propertyName . "\n";

            //$propertyType = 'string'; // Default type
            $propertyClass = null;

            // Try with reflection on the PHP type. Only look for classes,
            // because built in types convert better from the docblock.
            // Works for properties like:
            // `public GeojsonSchema $centroid;`
            if ($property->hasType()) {
                echo "Has type: " . $property->getType() . "\n";
                $type = $property->getType();
                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    $propertyClass = $type->getName();
                    $propertyType = 'object';
                    $schema['properties'][$propertyName] = JsonSchema::generateJsonSchema($propertyClass);
                    continue;
                }
            }

            // Move on to parsing the docblock.
            $docBlock = $property->getDocComment();

            // Check if the docblock specifies an array of classes. Works for
            // properties documented like:
            // @var SubcompartmentSchema[]
            if ($docBlock && strpos($property->getDocComment(), '@var') !== false) {
                preg_match('/@var Array<(\S+)(>)/', $property->getDocComment(), $matches);
                if (isset($matches[2]) && $matches[2] === '[]') {
                    $propertyClass = $matches[1];
                    $propertyType = 'array';
                    $schema['properties'][$propertyName] = [
                        'type' => 'array',
                        'items' => JsonSchema::generateJsonSchema($propertyClass),
                    ];
                    continue;
                }
            }

            // Move on to parsing @var statements in the docblock.

            if ($property->getDocComment()) {
                $varLookup = [];
                preg_match('/@var\s+(\S+)/', $property->getDocComment(), $varLookup);

                if(isset($varLookup[1])) {
                    echo "SOMONTHIS WA SET!!!! " . $varLookup[1]  . "\n";
                    $propertyType = $varLookup[1];
                }

                $schema['properties'][$propertyName] = [
                    'type' => $propertyType,
                ];
            }
        }

        return $schema;
    }
}
