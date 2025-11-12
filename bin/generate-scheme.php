<?php

declare(strict_types=1);

class SchemaGenerator
{
    private array $definitions = [];
    private string $outputDir;
    private string $sourceDir;
    private array $classSchemas = [];
    private bool $debug = true;
    private array $loadedClasses = [];
    private array $loadedFiles = [];
    private array $failedFiles = [];
    private array $excludeFolders = ['Examples', 'Traits', 'tests', 'Test'];
    private array $skipFiles = ['Std.php'];
    private array $classesBeforeLoad = [];

    public function __construct(string $outputDir, bool $debug = true)
    {
        $this->outputDir = rtrim($outputDir, '/');
        $this->debug = $debug;

        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        $this->log('=== Schema Generator Started ===');
        $this->log("Output directory: {$this->outputDir}");
    }

    /**
     * Process all PHP files in a directory recursively
     */
    public function processDirectory(string $sourceDir): array
    {
        $this->sourceDir = rtrim($sourceDir, '/');
        $this->classSchemas = [];
        $this->loadedFiles = [];
        $this->loadedClasses = [];
        $this->failedFiles = [];
        $this->classesBeforeLoad = array_merge(
            get_declared_classes(),
            get_declared_interfaces(),
            get_declared_traits()
        );

        if (!is_dir($this->sourceDir)) {
            throw new Exception("Source directory does not exist: {$this->sourceDir}");
        }

        $this->log("\nStarting directory scan: {$this->sourceDir}");

        $files = $this->findPhpFiles($this->sourceDir);
        $this->log('Found ' . count($files) . ' PHP files');

        if (empty($files)) {
            $this->log('WARNING: No PHP files found!');
            return [];
        }

        // Sort files intelligently: interfaces/traits first, then implementations
        $files = $this->prioritizeFileLoading($files);
        $this->log('Sorted files by dependency priority');

        // Load all files with retry loop
        $this->log("\n--- PHASE 1: Loading files (with retry) ---");
        $this->loadFilesWithRetry($files);

        // Process classes
        $this->log("\n--- PHASE 2: Generating schemas ---");
        $processedCount = 0;
        foreach ($files as $file) {
            $result = $this->processFile($file);
            if ($result) {
                $processedCount++;
            }
        }

        $this->log("\nSchemas generated from $processedCount files");
        $this->log('Total unique classes found: ' . count($this->classSchemas));

        // Summary of failed files
        if (!empty($this->failedFiles)) {
            $failCount = count($this->failedFiles);
            $this->log("\n--- Failed to load ($failCount files) ---");
            $shown = 0;
            foreach ($this->failedFiles as $file => $reason) {
                if ($shown < 15) {
                    $this->log('  ✗ ' . basename($file) . ": $reason");
                }
                $shown++;
            }
            if ($shown > 15) {
                $this->log('  ... and ' . ($shown - 15) . ' more');
            }
        }

        if (!empty($this->classSchemas)) {
            $this->log("\n--- PHASE 3: Saving schemas ---");
            $this->saveAllSchemas();
        } else {
            $this->log('ERROR: No classes were processed!');
        }

        return $this->classSchemas;
    }

    /**
     * Prioritize file loading: interfaces/traits first, then implementations
     */
    private function prioritizeFileLoading(array $files): array
    {
        $interfaces = [];
        $abstract = [];
        $implementations = [];

        foreach ($files as $file) {
            $fileName = basename($file);

            if ($this->shouldSkipFile($file)) {
                continue;
            }

            // Prioritize by filename pattern
            if (strpos($fileName, 'Interface.php') !== false) {
                $interfaces[] = $file;
            } elseif (strpos($fileName, 'Abstract.php') !== false || strpos($fileName, 'Trait.php') !== false) {
                $abstract[] = $file;
            } else {
                $implementations[] = $file;
            }
        }

        // Sort each category alphabetically
        sort($interfaces);
        sort($abstract);
        sort($implementations);

        // Return in priority order
        $sorted = array_merge($interfaces, $abstract, $implementations);

        $this->log('File priority: ' . count($interfaces) . ' interfaces, ' . count($abstract) . ' abstract/traits, ' . count($implementations) . ' implementations');

        return $sorted;
    }

    /**
     * Load files with retry loop
     */
    private function loadFilesWithRetry(array $files): void
    {
        $maxRetries = 25;
        $attempt = 0;

        do {
            $attempt++;
            $currentlyLoaded = count($this->loadedFiles);
            $currentlyFailed = count($this->failedFiles);
            $remaining = count($files) - $currentlyLoaded - $currentlyFailed;

            $this->log("\n[Attempt $attempt] Loaded: $currentlyLoaded, Failed: $currentlyFailed, Remaining: $remaining");

            $newLoadsThisAttempt = 0;

            foreach ($files as $file) {
                if (isset($this->loadedFiles[$file]) || isset($this->failedFiles[$file])) {
                    continue;
                }

                $result = $this->loadPhpFile($file);
                if ($result === true) {
                    $newLoadsThisAttempt++;
                }
            }

            $this->log("  Files loaded this attempt: $newLoadsThisAttempt");

            if ($newLoadsThisAttempt === 0) {
                $remaining = count($files) - count($this->loadedFiles) - count($this->failedFiles);
                if ($remaining > 0) {
                    $this->log("  ⚠️  No progress made. $remaining files remain (likely have external dependencies)");
                }
                break;
            }

        } while ((count($this->loadedFiles) + count($this->failedFiles)) < count($files) && $attempt < $maxRetries);

        $this->log("\n✓ Loading complete: " . count($this->loadedFiles) . '/' . count($files) . ' files loaded');
    }

    /**
     * Find all PHP files
     */
    private function findPhpFiles(string $directory): array
    {
        $files = [];

        try {
            $iterator = new RecursiveDirectoryIterator(
                $directory,
                RecursiveDirectoryIterator::SKIP_DOTS
            );

            $iterator = new RecursiveCallbackFilterIterator(
                $iterator,
                function ($current) {
                    $pathParts = explode(DIRECTORY_SEPARATOR, $current->getPathname());
                    foreach ($pathParts as $part) {
                        if (in_array($part, $this->excludeFolders, true)) {
                            return false;
                        }
                    }
                    return true;
                }
            );

            $recursiveIterator = new RecursiveIteratorIterator($iterator);
            $phpFiles = new RegexIterator(
                $recursiveIterator,
                '/^.+\.php$/i',
                RegexIterator::GET_MATCH
            );

            foreach ($phpFiles as $phpFile) {
                $files[] = $phpFile[0];
            }
        } catch (Exception $e) {
            $this->log('ERROR scanning directory: ' . $e->getMessage());
        }

        return $files;
    }

    /**
     * Check if file should be skipped
     */
    private function shouldSkipFile(string $filePath): bool
    {
        $fileName = basename($filePath);
        return in_array($fileName, $this->skipFiles, true);
    }

    /**
     * Load a PHP file and verify classes were defined
     */
    private function loadPhpFile(string $filePath): bool
    {
        if (!is_file($filePath) || isset($this->loadedFiles[$filePath])) {
            return false;
        }

        $fileName = basename($filePath);

        if ($this->shouldSkipFile($filePath)) {
            $this->loadedFiles[$filePath] = true;
            return false;
        }

        // Get existing entities
        $entitiesBefore = array_merge(
            get_declared_classes(),
            get_declared_interfaces(),
            get_declared_traits()
        );

        try {
            ob_start();

            $previousErrors = error_reporting(0);
            $errors = [];

            set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
                $errors[] = $errstr;
                return true;
            });

            @include_once $filePath;

            restore_error_handler();
            error_reporting($previousErrors);

            ob_end_clean();

            // Check what was defined
            $entitiesAfter = array_merge(
                get_declared_classes(),
                get_declared_interfaces(),
                get_declared_traits()
            );

            $newEntities = array_diff($entitiesAfter, $entitiesBefore);

            if (!empty($newEntities)) {
                $this->loadedFiles[$filePath] = true;
                $this->log('    ✓ ' . substr($fileName, 0, 35) . ' (' . count($newEntities) . ' entities)');
                return true;
            } else {
                if (!empty($errors) && strpos($errors[0], 'not found') !== false) {
                    $msg = substr($errors[0], 0, 60);
                    $this->failedFiles[$filePath] = "Error: $msg";
                    $this->log('    ✗ ' . substr($fileName, 0, 35) . ": $msg");
                } else {
                    $this->failedFiles[$filePath] = 'No entities defined';
                }
                return false;
            }

        } catch (Throwable $e) {
            ob_end_clean();
            restore_error_handler();

            $msg = substr($e->getMessage(), 0, 60);
            $this->failedFiles[$filePath] = $msg;
            return false;
        }
    }

    /**
     * Extract and process classes from file
     */
    private function processFile(string $filePath): bool
    {
        if ($this->shouldSkipFile($filePath)) {
            return false;
        }

        try {
            $content = @file_get_contents($filePath);
            if ($content === false) {
                return false;
            }

            $classes = $this->extractClassNamesFromFile($content);

            if (empty($classes)) {
                return false;
            }

            $fileName = basename($filePath);
            $this->log('Processing: ' . substr($fileName, 0, 40));

            $successCount = 0;
            foreach ($classes as $className) {
                if ($this->processClass($className, $filePath)) {
                    $successCount++;
                }
            }

            return $successCount > 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Process a single class
     */
    private function processClass(string $className, string $filePath): bool
    {
        try {
            if (!class_exists($className, false)) {
                return false;
            }

            if (isset($this->loadedClasses[$className])) {
                return false;
            }

            if (interface_exists($className, false) || trait_exists($className, false)) {
                return false;
            }

            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract()) {
                return false;
            }

            $schema = $this->generateSchema($className);

            if (!empty($schema)) {
                $this->classSchemas[$className] = [
                    'file' => $filePath,
                    'schema' => $schema
                ];
                $this->loadedClasses[$className] = true;
                $shortName = basename(str_replace('\\', '/', $className));
                $this->log('    ✓ ' . substr($shortName, 0, 40));
                return true;
            }

            return false;

        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Extract class names from file
     */
    private function extractClassNamesFromFile(string $content): array
    {
        $classes = [];
        $namespace = '';

        if (preg_match('/namespace\s+([a-zA-Z0-9_\\\\]+);/', $content, $nsMatches)) {
            $namespace = trim($nsMatches[1]) . '\\';
        }

        if (preg_match_all(
            '/^\s*(?!abstract\s)(?!interface\s)(?!trait\s)class\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*(?:extends|implements|{)/m',
            $content,
            $matches
        )) {
            foreach ($matches[1] as $className) {
                $fullClassName = $namespace . trim($className);
                if (!empty($fullClassName)) {
                    $classes[] = $fullClassName;
                }
            }
        }

        return $classes;
    }

    /**
     * Generate schema for class
     */
    public function generateSchema(string $className): array
    {
        $this->definitions = [];

        try {
            if (!class_exists($className)) {
                return [];
            }

            $reflection = new ReflectionClass($className);
            return $this->buildSchema($reflection);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Build schema recursively
     */
    private function buildSchema(ReflectionClass $class): array
    {
        $schema = [
            'type' => 'object',
            'title' => $class->getShortName(),
            'description' => 'Schema for ' . $class->getName(),
            'properties' => [],
            'required' => []
        ];

        try {
            foreach ($class->getProperties() as $property) {
                $propName = $property->getName();

                try {
                    $type = $property->getType();

                    if ($type === null) {
                        $schema['properties'][$propName] = ['type' => 'mixed'];
                    } elseif ($type->isBuiltin()) {
                        $schema['properties'][$propName] = [
                            'type' => $this->mapPhpTypeToJsonType((string)$type),
                            'nullable' => $type->allowsNull()
                        ];
                    } else {
                        $refClassName = $type->getName();
                        $shortRefName = basename(str_replace('\\', '/', $refClassName));

                        $schema['properties'][$propName] = [
                            '$ref' => '#/definitions/' . $shortRefName
                        ];

                        if (!isset($this->definitions[$shortRefName])) {
                            try {
                                if (class_exists($refClassName)) {
                                    $this->definitions[$shortRefName] = $this->buildSchema(
                                        new ReflectionClass($refClassName)
                                    );
                                }
                            } catch (Exception $e) {
                                // Ignore
                            }
                        }
                    }

                    $isOptional = false;
                    try {
                        $isOptional = $property->getDefaultValue() !== null || $type?->allowsNull();
                    } catch (Exception $e) {
                        // Ignore
                    }

                    if (!$isOptional) {
                        $schema['required'][] = $propName;
                    }

                } catch (Exception $e) {
                    // Skip
                }
            }

        } catch (Exception $e) {
            // Ignore
        }

        return $schema;
    }

    /**
     * Map PHP types to JSON types
     */
    private function mapPhpTypeToJsonType(string $phpType): string
    {
        $typeMap = [
            'int' => 'integer', 'float' => 'number', 'bool' => 'boolean',
            'string' => 'string', 'array' => 'array', 'mixed' => 'object'
        ];

        return $typeMap[$phpType] ?? 'object';
    }

    /**
     * Save all schemas
     */
    private function saveAllSchemas(): void
    {
        $this->log('Saving ' . count($this->classSchemas) . ' schemas...');

        $globalSchema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'title' => 'Global Application Schema',
            'definitions' => [],
            'classes' => []
        ];

        $savedCount = 0;
        foreach ($this->classSchemas as $className => $data) {
            $schema = $data['schema'];

            if (empty($schema)) {
                continue;
            }

            $shortName = basename(str_replace('\\', '/', $className));
            $globalSchema['definitions'][$shortName] = $schema;
            $globalSchema['classes'][$className] = $shortName;

            $fileName = $this->sanitizeFileName($shortName) . '.json';
            $filePath = $this->outputDir . '/' . $fileName;

            $individualSchema = [
                '$schema' => 'http://json-schema.org/draft-07/schema#',
                'title' => $shortName,
                '$ref' => '#/definitions/' . $shortName,
                'definitions' => [$shortName => $schema]
            ];

            $json = json_encode($individualSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($json !== false && file_put_contents($filePath, $json)) {
                $savedCount++;
            }
        }

        $this->log("Saved $savedCount schema files");

        $globalFilePath = $this->outputDir . '/global-schema.json';
        $globalJson = json_encode($globalSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($globalJson !== false) {
            file_put_contents($globalFilePath, $globalJson);
            $this->log('✓ global-schema.json (' . count($globalSchema['classes']) . ' classes)');
        }

        $this->generateIndexFile($globalSchema);
    }

    /**
     * Generate index
     */
    private function generateIndexFile(array $globalSchema): void
    {
        $totalSchemas = count($globalSchema['classes']);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Schema Index</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .schema-list { list-style: none; padding: 0; }
        .schema-list li { margin: 10px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #007bff; }
        .schema-list a { color: #007bff; text-decoration: none; font-weight: bold; }
        .schema-list a:hover { text-decoration: underline; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generated JSON Schemas</h1>
        <p>Total schemas: <strong>{$totalSchemas}</strong></p>
        <p style="font-size: 12px; color: #999;">Generated: {$this->getCurrentTimestamp()}</p>
        
        <h2>Schemas</h2>
HTML;

        if (!empty($globalSchema['classes'])) {
            $html .= "<ul class=\"schema-list\">\n";
            foreach ($globalSchema['classes'] as $className => $shortName) {
                $fileName = $this->sanitizeFileName($shortName) . '.json';
                $html .= "<li><code>{$className}</code> - <a href=\"{$fileName}\">View</a></li>\n";
            }
            $html .= "</ul>\n";
        }

        $html .= "<h2>Global Schema</h2><p><a href=\"global-schema.json\">View Global Schema</a></p></div></body></html>";

        file_put_contents($this->outputDir . '/index.html', $html);
        $this->log('✓ index.html');
    }

    private function sanitizeFileName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
    }

    private function getCurrentTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function log(string $message): void
    {
        if ($this->debug) {
            echo '[' . date('H:i:s') . "] {$message}\n";
        }
    }
}

// Usage
$sourceDir = $argv[1] ?? __DIR__ . '/../src';
$outputDir = $argv[2] ?? __DIR__ . '/../schemas';
require_once __DIR__ . '/../src/Traits/ProtobufSerializable.php';

$generator = new SchemaGenerator($outputDir, true);

try {
    $results = $generator->processDirectory($sourceDir);

    echo "\n" . str_repeat('=', 50) . "\n";
    echo "COMPLETE\n";
    echo str_repeat('=', 50) . "\n";
    echo 'Schemas generated: ' . count($results) . "\n";
    echo "Output: {$outputDir}\n";
    echo str_repeat('=', 50) . "\n";

    echo "\n\nГенератор по JSON Schemes: https://quicktype.io/ (есть в docker) \n\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}
