#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Это прототип, генерация описания пакетов
 * по стандарту Google ProtoBuf
 * https://protobuf.dev/
 *
 * Как запускать:
 * php ./bin/generate-proto.php --debug
 */
class SimpleAutoloader
{
    private string $srcDir;
    private array $classMap = [];
    private array $interfaceMap = [];
    private array $traitMap = [];
    private array $loaded = [];
    private array $errors = [];
    private bool $debug = false;

    public function __construct(string $srcDir, bool $debug = false)
    {
        $this->srcDir = rtrim($srcDir, '/');
        $this->debug = $debug;

        $this->buildClassMap();
        spl_autoload_register([$this, 'autoload'], true, true);
    }

    private function buildClassMap(): void
    {
        if (!is_dir($this->srcDir)) {
            throw new RuntimeException("Source directory not found: {$this->srcDir}");
        }

        $this->scanDirectory($this->srcDir);

        if ($this->debug) {
            echo "📊 Autoloader statistics:\n";
            echo '  Classes: ' . count($this->classMap) . "\n";
            echo '  Interfaces: ' . count($this->interfaceMap) . "\n";
            echo '  Traits: ' . count($this->traitMap) . "\n";
            echo "\n";
        }
    }

    private function scanDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        try {
            $iterator = new RecursiveDirectoryIterator(
                $dir,
                RecursiveDirectoryIterator::SKIP_DOTS
            );
            $recursiveIterator = new RecursiveIteratorIterator($iterator);

            foreach ($recursiveIterator as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $this->mapClassFromFile((string)$file);
            }
        } catch (Exception $e) {
            $this->logError("Error scanning directory: {$e->getMessage()}");
        }
    }

    private function mapClassFromFile(string $filePath): void
    {
        try {
            $content = file_get_contents($filePath);

            if ($content === false) {
                $this->logError("Cannot read file: $filePath");
                return;
            }

            if (!preg_match('/namespace\s+([^;]+);/', $content, $nsMatch)) {
                return;
            }

            $namespace = trim($nsMatch[1]);

            if (preg_match_all('/interface\s+(\w+)(?:\s+extends|\s*{|$)/', $content, $matches)) {
                foreach ($matches[1] as $name) {
                    $fullName = $namespace . '\\' . $name;
                    $this->interfaceMap[$fullName] = $filePath;
                }
            }

            if (preg_match_all('/trait\s+(\w+)(?:\s*{|$)/', $content, $matches)) {
                foreach ($matches[1] as $name) {
                    $fullName = $namespace . '\\' . $name;
                    $this->traitMap[$fullName] = $filePath;
                }
            }

            if (preg_match_all('/class\s+(\w+)(?:\s+extends|\s+implements|\s*{|$)/', $content, $matches)) {
                foreach ($matches[1] as $name) {
                    $fullName = $namespace . '\\' . $name;
                    $this->classMap[$fullName] = $filePath;
                }
            }
        } catch (Exception $e) {
            $this->logError("Error mapping class from file $filePath: {$e->getMessage()}");
        }
    }

    public function autoload(string $class): bool
    {
        if (isset($this->loaded[$class])) {
            return true;
        }

        $filePath = $this->findClassFile($class);

        if (!$filePath) {
            return false;
        }

        try {
            $this->loadFile($filePath);
            $this->loaded[$class] = true;
            return true;
        } catch (Exception $e) {
            $this->logError("Failed to autoload $class: {$e->getMessage()}");
            return false;
        }
    }

    private function findClassFile(string $class): ?string
    {
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        if (isset($this->interfaceMap[$class])) {
            return $this->interfaceMap[$class];
        }

        if (isset($this->traitMap[$class])) {
            return $this->traitMap[$class];
        }

        return null;
    }

    private function loadFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: $filePath");
        }

        if (!is_readable($filePath)) {
            throw new RuntimeException("File not readable: $filePath");
        }

        $previousErrorReporting = error_reporting(E_ALL);
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new RuntimeException("PHP Error: $errstr in $errfile:$errline");
        });

        try {
            require_once $filePath;
        } finally {
            restore_error_handler();
            error_reporting($previousErrorReporting);
        }
    }

    public function loadNamespace(string $namespace): int
    {
        $loaded = 0;
        $namespace = rtrim($namespace, '\\');

        $classesToLoad = [];

        foreach ($this->classMap as $class => $file) {
            if (strpos($class, $namespace) === 0) {
                $classesToLoad[] = $class;
            }
        }

        // Load interfaces and traits first
        foreach ($this->interfaceMap as $interface => $file) {
            if (strpos($interface, $namespace) === 0) {
                if (!interface_exists($interface, false)) {
                    try {
                        $this->loadFile($file);
                        $loaded++;
                    } catch (Exception $e) {
                        $this->logError("Failed to load interface $interface: {$e->getMessage()}");
                    }
                }
            }
        }

        foreach ($this->traitMap as $trait => $file) {
            if (strpos($trait, $namespace) === 0) {
                if (!trait_exists($trait, false)) {
                    try {
                        $this->loadFile($file);
                        $loaded++;
                    } catch (Exception $e) {
                        $this->logError("Failed to load trait $trait: {$e->getMessage()}");
                    }
                }
            }
        }

        // Then load classes with multiple passes
        $maxPasses = 5;
        $pass = 0;

        while (!empty($classesToLoad) && $pass < $maxPasses) {
            $pass++;
            $loadedThisPass = 0;

            foreach ($classesToLoad as $key => $class) {
                if (class_exists($class, false)) {
                    unset($classesToLoad[$key]);
                    continue;
                }

                try {
                    $file = $this->classMap[$class];
                    $this->loadFile($file);
                    unset($classesToLoad[$key]);
                    $loadedThisPass++;
                    $loaded++;
                } catch (Exception $e) {
                    if ($this->debug) {
                        echo "  [Pass $pass] Skipping $class: {$e->getMessage()}\n";
                    }
                }
            }

            if ($loadedThisPass === 0 && !empty($classesToLoad)) {
                break;
            }
        }

        return $loaded;
    }

    public function getClasses(): array
    {
        return array_keys($this->classMap);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function logError(string $message): void
    {
        $this->errors[] = $message;
        if ($this->debug) {
            echo "  ❌ $message\n";
        }
    }
}

// ============================================================================
// Proto Generator - SINGLE FILE ONLY
// ============================================================================

use Ypmn\Traits\ProtobufSerializable;

class ProtobufGenerator
{
    private string $outputDir;
    private string $namespace;
    private string $packageName;
    private array $classes = [];
    private SimpleAutoloader $autoloader;
    private bool $debug = false;
    private int $messagesGenerated = 0;

    public function __construct(
        SimpleAutoloader $autoloader,
        string           $namespace = 'Ypmn',
        string           $outputDir = './proto'
    )
    {
        $this->autoloader = $autoloader;
        $this->namespace = $namespace;
        $this->packageName = strtolower($namespace);
        $this->outputDir = rtrim($outputDir, '/');
        $this->debug = in_array('--debug', $_SERVER['argv'] ?? []);

        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    /**
     * Clean up old proto files before generation
     */
    public function cleanupOldFiles(): void
    {
        echo "🧹 Cleaning up old .proto files in {$this->outputDir}...\n";

        $protoFiles = glob($this->outputDir . '/*.proto');
        if (!empty($protoFiles)) {
            foreach ($protoFiles as $file) {
                if (unlink($file)) {
                    echo '   ✓ Deleted: ' . basename($file) . "\n";
                }
            }
        } else {
            echo "   (No old files found)\n";
        }
    }

    public function discoverClasses(): void
    {
        echo "🔍 Loading namespace: {$this->namespace}...\n";
        $loaded = $this->autoloader->loadNamespace($this->namespace);
        echo "   Loaded $loaded classes and dependencies\n\n";

        $allClasses = $this->autoloader->getClasses();
        echo '📋 Checking ' . count($allClasses) . " discovered classes...\n";

        $protobufClasses = 0;
        $checkedClasses = 0;

        foreach ($allClasses as $class) {
            if (!class_exists($class, false)) {
                continue;
            }

            $checkedClasses++;

            try {
                $reflection = new ReflectionClass($class);

                if ($reflection->isInterface() || $reflection->isAbstract() || $reflection->isTrait()) {
                    if ($this->debug) {
                        $type = $reflection->isInterface() ? 'interface' : ($reflection->isAbstract() ? 'abstract' : 'trait');
                        echo "  ⏭️  Skipping $class ($type)\n";
                    }
                    continue;
                }

                $traitNames = $reflection->getTraitNames();

                if ($this->debug && empty($traitNames)) {
                    echo "  ⏭️  $class has no traits\n";
                }

                if (in_array(ProtobufSerializable::class, $traitNames)) {
                    $this->classes[] = $class;
                    $protobufClasses++;
                    echo "  ✅ Found: $class\n";
                }
            } catch (ReflectionException $e) {
                if ($this->debug) {
                    echo "  ❌ Cannot reflect $class: {$e->getMessage()}\n";
                }
                continue;
            }
        }

        echo "\n📊 Summary:\n";
        echo "   Checked: $checkedClasses classes\n";
        echo "   Found: $protobufClasses with ProtobufSerializable trait\n\n";

        if ($protobufClasses === 0) {
            echo "⚠️  No classes found!\n\n";
        }
    }

    /**
     * Generate SINGLE combined .proto file with all messages
     * This method ONLY creates ONE file: ypmn.proto
     */
    public function generateSingleProtoFile(): void
    {
        if (empty($this->classes)) {
            echo "⚠️  No classes found to generate\n";
            return;
        }

        echo "📝 Generating single combined .proto file...\n";

        // Collect all messages
        $messages = [];

        foreach ($this->classes as $class) {
            try {
                if (!method_exists($class, 'toProto3Message')) {
                    echo "⚠️  Skipping $class - no toProto3Message method\n";
                    continue;
                }

                $messageContent = $class::toProto3Message();
                $definition = $class::getProtobufDefinition();

                $messages[] = [
                    'name' => $definition['name'],
                    'content' => $messageContent,
                    'class' => $class,
                ];

                echo "  ✓ Collected message: {$definition['name']}\n";
            } catch (Exception $e) {
                echo "  ❌ Error collecting message for $class: {$e->getMessage()}\n";
            }
        }

        if (empty($messages)) {
            echo "⚠️  No messages collected to generate\n";
            return;
        }

        // Create ONLY ONE FILE
        $fileName = $this->packageName . '.proto';
        $filePath = $this->outputDir . '/' . $fileName;

        // Build the content
        $protoContent = $this->buildProtoFile($messages);

        // Write to file
        $bytesWritten = file_put_contents($filePath, $protoContent);

        if ($bytesWritten === false) {
            echo "❌ ERROR: Failed to write file: $filePath\n";
            return;
        }

        $this->messagesGenerated = count($messages);

        echo "\n✅ SUCCESS! Generated single proto file:\n";
        echo "   📄 $filePath\n";
        echo "   📊 Messages: {$this->messagesGenerated}\n";
        echo '   💾 Size: ' . $this->formatBytes($bytesWritten) . "\n";

        // VERIFY: Show what files exist now
        echo "\n🔍 Verification - Files in {$this->outputDir}:\n";
        $allProtoFiles = glob($this->outputDir . '/*.proto');
        if (empty($allProtoFiles)) {
            echo "   ❌ ERROR: No .proto files found!\n";
        } else {
            foreach ($allProtoFiles as $file) {
                $size = filesize($file);
                echo '   ✓ ' . basename($file) . ' (' . $this->formatBytes($size) . ")\n";
            }
            echo '   Total files: ' . count($allProtoFiles) . "\n";
        }
    }

    /**
     * Build complete proto file with all messages
     */
    private function buildProtoFile(array $messages): string
    {
        $proto = "syntax = \"proto3\";\n\n";
        $proto .= "package {$this->packageName};\n\n";
        $proto .= "option php_namespace = \"" . ucfirst($this->namespace) . "\";\n";
        $proto .= "option go_package = \"github.com/yourpayments/go-api-client\";\n";
        $proto .= "option java_package = \"ru.ypmn.api\";\n";
        $proto .= "option java_multiple_files = true;\n\n";

        // Add all messages
        foreach ($messages as $message) {
            $proto .= $message['content'];
        }

        return $this->cleanupProtoContent($proto);
    }

    public function getGeneratedProtoFiles(): array
    {
        return glob($this->outputDir . '/*.proto') ?: [];
    }

    public function printInstructions(): void
    {
        $protoFiles = $this->getGeneratedProtoFiles();

        if (empty($protoFiles)) {
            echo "⚠️  No proto file generated!\n";
            return;
        }

        echo "\n📋 Protobuf resources:";
        echo "\n https://protobuf.dev/";
        echo "\n https://capnproto.org/\n\n";
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return "$bytes B";
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return round($bytes / (1024 * 1024), 2) . ' MB';
    }

    public function getStats(): array
    {
        return [
            'classes_found' => count($this->classes),
            'messages_generated' => $this->messagesGenerated,
            'proto_files_created' => count($this->getGeneratedProtoFiles()),
            'output_dir' => $this->outputDir,
        ];
    }

    /**
     * Clean up the generated proto content
     * Removes unwanted patterns from the final output
     */
    private function cleanupProtoContent(string $content): string
    {
        // Хак + TODO: упростить интерфейсы выплат
        $content = str_replace('Destination destination', 'PayoutMobileDestination destination', $content);

        // Remove question marks (nullable type indicators)
        $content = str_replace('?', '', $content);

        // Remove Interface suffix from message types and field types
        $content = preg_replace('/\bInterface\b/', '', $content);

        // Remove namespace prefixes (Ypmn\ at the start of type names)
        // Match type names after: "message ", ": ", "< ", and space before field name
        $content = preg_replace('/(\s)(Ypmn\\\\)/', '$1', $content);
        $content = preg_replace('/(:\s)(Ypmn\\\\)/', '$1', $content);
        return trim($content);
    }
}

// ============================================================================
// Main execution
// ============================================================================

$srcDir = dirname(__DIR__) . '/src';
$namespace = 'Ypmn';
$debug = in_array('--debug', $argv);

echo "🚀 YourPayments Protobuf Generator\n";
echo "==================================================\n\n";

try {
    echo "📂 Initializing autoloader from: $srcDir\n";
    $autoloader = new SimpleAutoloader($srcDir, $debug);

    $generator = new ProtobufGenerator($autoloader, $namespace, './proto');

    // STEP 1: Clean up old files
    echo "\n";
    $generator->cleanupOldFiles();

    // STEP 2: Discover classes
    echo "\n";
    $generator->discoverClasses();

    // STEP 3: Generate proto file
    echo "\n";
    $generator->generateSingleProtoFile();

    // STEP 4: Statistics
    echo "\n📊 Statistics:\n";
    $stats = $generator->getStats();
    foreach ($stats as $key => $value) {
        echo "  $key: $value\n";
    }

    // STEP 5: Instructions
    echo "\n";
    $generator->printInstructions();

    // Print any errors
    $errors = $autoloader->getErrors();
    if (!empty($errors)) {
        echo "\n⚠️  Warnings/Errors encountered:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }

} catch (Exception $e) {
    echo "\n❌ Fatal error: {$e->getMessage()}\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
