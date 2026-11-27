<?php

declare(strict_types=1);

namespace Ypmn\Tests;

use Psr\Log\AbstractLogger;

/** Логгер, который складывает записи в память */
final class RecordingLogger extends AbstractLogger
{
    /** @var array<int, array{level: string, message: string, context: array}> */
    public array $records = [];

    public function log($level, $message, array $context = []): void
    {
        $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
    }
}
