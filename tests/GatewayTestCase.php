<?php

declare(strict_types=1);

namespace Ypmn\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ypmn\ApiRequest;
use Ypmn\Merchant;

/**
 * Базовый класс тестов: поднимает локальную заглушку шлюза,
 * чтобы проверять настоящие HTTP-запросы SDK, а не моки cURL.
 */
abstract class GatewayTestCase extends TestCase
{
    private const START_TIMEOUT_SECONDS = 5.0;

    /** @var resource|null */
    private static $server = null;

    private static string $host = '';

    public static function setUpBeforeClass(): void
    {
        $port = self::findFreePort();
        self::$host = 'http://127.0.0.1:' . $port;
        self::$server = proc_open(
            [PHP_BINARY, '-S', '127.0.0.1:' . $port, __DIR__ . '/fixtures/gateway.php'],
            [['file', '/dev/null', 'r'], ['file', '/dev/null', 'w'], ['file', '/dev/null', 'w']],
            $pipes
        );

        $deadline = microtime(true) + self::START_TIMEOUT_SECONDS;

        while (microtime(true) < $deadline) {
            $socket = @fsockopen('127.0.0.1', $port);

            if ($socket !== false) {
                fclose($socket);

                return;
            }

            usleep(50000);
        }

        throw new RuntimeException('Заглушка шлюза не запустилась');
    }

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$server)) {
            proc_terminate(self::$server);
            proc_close(self::$server);
        }

        self::$server = null;
    }

    protected function createApiRequest(): ApiRequest
    {
        return (new ApiRequest(new Merchant('testmerchant', 'testsecret')))->setHost(self::$host);
    }

    /** Разобрать ответ заглушки: она возвращает запрос в том виде, в каком его получила */
    protected function decodeEcho(array $result): array
    {
        return json_decode((string) $result['response'], true, 512, JSON_THROW_ON_ERROR);
    }

    private static function findFreePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $name = stream_socket_get_name($socket, false);
        fclose($socket);

        return (int) substr($name, strrpos($name, ':') + 1);
    }
}
