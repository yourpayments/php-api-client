<?php

namespace Ypmn\Traits;

use ReflectionClass;
use ReflectionProperty;

/**
 * Universal trait for automatic protobuf generation from PHP classes.
 * Generates clean, validated proto3 message definitions.
 */
trait ProtobufSerializable
{
    /**
     * Maps PHP types to Protobuf scalar types
     */
    private static array $typeMapping = [
        'string' => 'string',
        'int' => 'int32',
        'integer' => 'int32',
        'int64' => 'int64',
        'bool' => 'bool',
        'boolean' => 'bool',
        'float' => 'double',
        'double' => 'double',
        'array' => 'bytes',
        'mixed' => 'bytes',
    ];

    /**
     * Get protobuf message definition for this class
     */
    public static function getProtobufDefinition(): array
    {
        $reflection = new ReflectionClass(static::class);
        $properties = $reflection->getProperties(
            ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PRIVATE
        );

        $fields = [];
        $fieldNumber = 1;

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $fieldDef = self::extractFieldDefinition($property, $fieldNumber++);
            if ($fieldDef) {
                $fields[] = $fieldDef;
            }
        }

        return [
            'class' => $reflection->getName(),
            'namespace' => $reflection->getNamespaceName(),
            'name' => $reflection->getShortName(),
            'fields' => $fields,
            'package' => self::derivePackage($reflection->getNamespaceName()),
            'description' => self::extractClassDescription($reflection),
        ];
    }

    /**
     * Extract class description from docblock
     */
    private static function extractClassDescription(ReflectionClass $reflection): string
    {
        $docComment = $reflection->getDocComment();
        if (!$docComment) {
            return '';
        }

        return self::cleanDocblockText($docComment);
    }

    /**
     * Clean docblock text - remove markers and extract main description
     */
    private static function cleanDocblockText(?string $docComment): string
    {
        if (!$docComment) {
            return '';
        }

        // Remove /** */ markers
        $text = preg_replace('/^\/\*+\s*/', '', $docComment);
        $text = preg_replace('/\s*\*+\/$/', '', $text);

        // Split into lines
        $lines = explode("\n", $text);

        $cleaned = [];

        foreach ($lines as $line) {
            // Remove leading * and space
            $line = preg_replace('/^\s*\*\s?/', '', $line);
            $line = trim($line);

            // Stop at first @annotation
            if (str_starts_with($line, '@')) {
                break;
            }

            // Add non-empty lines
            if (!empty($line)) {
                $cleaned[] = $line;
            }
        }

        // Join lines
        $result = implode(' ', $cleaned);

        // Clean multiple spaces
        $result = preg_replace('/\s+/', ' ', $result);

        return trim($result);
    }

    /**
     * Extract field definition from a property
     */
    private static function extractFieldDefinition(ReflectionProperty $property, int $fieldNumber): ?array
    {
        $propertyName = $property->getName();
        $docComment = $property->getDocComment();

        // Extract type from docblock @var
        $type = self::extractTypeFromDocblock($docComment);

        // If not found, try PHP typed property
        if (!$type && $property->hasType()) {
            $type = (string)$property->getType();
        }

        if (!$type) {
            return null;
        }

        // Extract description
        $description = self::cleanDocblockText($docComment);

        $isRepeated = self::isRepeatedField($docComment, $type);
        $isOptional = self::isOptionalField($docComment, $property);

        $protoType = self::mapToProtobufType($type);

        return [
            'number' => $fieldNumber,
            'type' => $protoType,
            'name' => self::phpNameToProtoName($propertyName),
            'originalName' => $propertyName,
            'label' => $isRepeated ? 'repeated' : ($isOptional ? 'optional' : ''),
            'description' => $description,
        ];
    }

    /**
     * Extract @var type from docblock
     */
    private static function extractTypeFromDocblock(?string $docComment): ?string
    {
        if (!$docComment) {
            return null;
        }

        // Match @var Type or @var Type[]
        if (preg_match('/@var\s+(\S+?)(?:\[\])?(?:\|null)?(?:\s|$)/', $docComment, $matches)) {
            $type = $matches[1];
            // Remove array brackets
            $type = str_replace('[]', '', $type);
            // Handle nullable types (Type|null)
            if (strpos($type, '|') !== false) {
                $parts = explode('|', $type);
                $type = trim($parts[0]);
            }
            return $type;
        }

        return null;
    }

    /**
     * Check if field is repeated (array/collection)
     */
    private static function isRepeatedField(?string $docComment, string $type): bool
    {
        if (!$docComment) {
            return false;
        }

        if (preg_match('/@var\s+\S+\[\]/', $docComment)) {
            return true;
        }

        return in_array($type, ['array', 'Collection', 'ArrayCollection', 'SplFixedArray', 'ArrayList']);
    }

    /**
     * Check if field is optional
     */
    private static function isOptionalField(?string $docComment, ReflectionProperty $property): bool
    {
        if (!$docComment) {
            return false;
        }

        if (preg_match('/@var\s+\S+\|null/', $docComment)) {
            return true;
        }

        if ($property->hasDefaultValue()) {
            return $property->getDefaultValue() === null;
        }

        return false;
    }

    /**
     * Map PHP type to Protobuf type with cleanup
     * Removes: ?, Interface suffix, namespace prefixes
     */
    private static function mapToProtobufType(string $phpType): string
    {
        // CLEANUP: Remove question marks (nullable types)
        $phpType = str_replace('?', '', $phpType);

        // CLEANUP: Remove Interface suffix
        $phpType = preg_replace('/Interface$/', '', $phpType);

        // CLEANUP: Remove namespace prefix (keep only class name)
        if (strpos($phpType, '\\') !== false) {
            $parts = explode('\\', $phpType);
            $phpType = array_pop($parts);
        }

        // CLEANUP: Trim whitespace
        $phpType = trim($phpType);

        // Handle fully qualified class names
        if (class_exists($phpType) || interface_exists($phpType)) {
            $reflection = new ReflectionClass($phpType);
            $className = $reflection->getShortName();

            // CLEANUP: Remove Interface suffix from reflected name
            $className = preg_replace('/Interface$/', '', $className);

            return $className;
        }

        // Handle array types
        if (str_ends_with($phpType, '[]')) {
            $baseType = substr($phpType, 0, -2);
            return self::mapToProtobufType($baseType);
        }

        // Map primitive types
        $lowerType = strtolower($phpType);
        return self::$typeMapping[$lowerType] ?? $phpType;
    }

    /**
     * Convert PHP property name to proto field name (snake_case)
     */
    private static function phpNameToProtoName(string $name): string
    {
        // Remove leading underscore if present
        $name = ltrim($name, '_');

        // Convert camelCase to snake_case
        $name = preg_replace('/([A-Z])/', '_$1', $name);
        $name = strtolower($name);

        return ltrim($name, '_');
    }

    /**
     * Derive package name from namespace
     */
    private static function derivePackage(string $namespace): string
    {
        if (empty($namespace)) {
            return 'default';
        }

        $parts = explode('\\', $namespace);
        return strtolower(implode('.', $parts));
    }

    /**
     * Format text as proto3 comment
     */
    private static function formatProtoComment(string $text): string
    {
        if (empty(trim($text))) {
            return '';
        }

        $text = trim($text);

        if (strlen($text) < 70) {
            return "// $text\n";
        }

        // Split by sentences for long text
        $parts = preg_split('/(\.\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $output = '';
        $currentComment = '';

        for ($i = 0; $i < count($parts); $i += 2) {
            $sentence = trim($parts[$i] ?? '');
            $separator = $parts[$i + 1] ?? '';

            if (empty($sentence)) {
                continue;
            }

            $testComment = $currentComment . (empty($currentComment) ? '' : ' ') . $sentence . $separator;

            if (strlen($testComment) > 80) {
                if (!empty($currentComment)) {
                    $output .= "// $currentComment\n";
                }
                $currentComment = $sentence . $separator;
            } else {
                $currentComment = $testComment;
            }
        }

        if (!empty($currentComment)) {
            $output .= "// $currentComment\n";
        }

        return $output;
    }

    /**
     * Get only the message definition (without file headers)
     */
    public static function toProto3Message(): string
    {
        $def = static::getProtobufDefinition();

        $proto = '';

        // Add message description
        if (!empty($def['description'])) {
            $proto .= self::formatProtoComment($def['description']);
        }

        $proto .= "message {$def['name']} {\n";

        foreach ($def['fields'] as $field) {
            // Add field description
            if (!empty($field['description'])) {
                $proto .= '  ' . self::formatProtoComment($field['description']);
            }

            $label = !empty($field['label']) ? "{$field['label']} " : '';
            $proto .= "  {$label}{$field['type']} {$field['name']} = {$field['number']};\n";
        }

        $proto .= "}\n\n";

        return $proto;
    }

    /**
     * Get complete protobuf message in proto3 format (standalone)
     */
    public static function toProto3(): string
    {
        $def = static::getProtobufDefinition();

        $proto = "syntax = \"proto3\";\n\n";
        $proto .= "package {$def['package']};\n\n";
        $proto .= "option php_namespace = \"{$def['namespace']}\";\n";
        $proto .= "option go_package = \"github.com/yourpayments/go-api-client\";\n";
        $proto .= "option java_package = \"ru.ypmn.api\";\n";
        $proto .= "option java_multiple_files = true;\n\n";

        $proto .= static::toProto3Message();

        return $proto;
    }

    /**
     * Validate generated proto message
     */
    public static function validateProto(): array
    {
        $errors = [];
        $def = static::getProtobufDefinition();

        if (empty($def['fields'])) {
            $errors[] = 'Message has no fields';
            return $errors;
        }

        // Check for duplicate field numbers
        $fieldNumbers = [];
        foreach ($def['fields'] as $field) {
            if (isset($fieldNumbers[$field['number']])) {
                $errors[] = "Duplicate field number {$field['number']}";
            }
            $fieldNumbers[$field['number']] = true;
        }

        // Check field numbers are in valid range
        foreach ($def['fields'] as $field) {
            $num = $field['number'];
            if ($num < 1 || $num > 536870911) {
                $errors[] = "Field number $num out of valid range";
            }

            // Check for reserved field numbers (19000-19999)
            if ($num >= 19000 && $num <= 19999) {
                $errors[] = "Field number $num is reserved";
            }
        }

        return $errors;
    }

    /**
     * Get all classes in namespace that use ProtobufSerializable
     */
    public static function discoverProtobufClasses(string $namespace): array
    {
        $classes = [];

        foreach (get_declared_classes() as $class) {
            if (strpos($class, $namespace) === 0) {
                try {
                    $reflection = new ReflectionClass($class);
                    if (in_array(self::class, $reflection->getTraitNames() ?? [])) {
                        $classes[] = $class;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $classes;
    }
}
