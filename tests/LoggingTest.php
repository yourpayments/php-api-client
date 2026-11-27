<?php

declare(strict_types=1);

namespace Ypmn\Tests;

use Psr\Log\AbstractLogger;
use RuntimeException;
use Ypmn\PaymentException;

final class LoggingTest extends GatewayTestCase
{
    private const CARD_BODY = '{"paymentMethod":"CCVISAMC","cardDetails":{"number":"4111111111111111","cvv":"123","owner":"IVAN"}}';

    public function testLogsRequestAndResponseWithSharedRequestId(): void
    {
        $logger = new RecordingLogger();

        $this->createApiRequest()->setLogger($logger)->sendPostRequest('{"a":1}', '/echo?x=1');

        self::assertCount(2, $logger->records);
        [$request, $response] = $logger->records;
        self::assertSame('debug', $request['level']);
        self::assertSame('POST', $request['context']['method']);
        self::assertStringEndsWith('/echo?x=1', $request['context']['url']);
        self::assertSame('info', $response['level']);
        self::assertSame(200, $response['context']['http_code']);
        self::assertIsFloat($response['context']['duration_ms']);
        self::assertSame($request['context']['request_id'], $response['context']['request_id']);
    }

    public function testCardDataAndSignatureOfRequestNeverReachTheLog(): void
    {
        $logger = new RecordingLogger();
        $apiRequest = $this->createApiRequest()->setLogger($logger);

        $echo = $this->decodeEcho($apiRequest->sendPostRequest(self::CARD_BODY, '/echo'));

        $loggedRequest = stripslashes(json_encode($logger->records[0], JSON_THROW_ON_ERROR));
        self::assertStringNotContainsString('4111111111111111', $loggedRequest);
        self::assertStringNotContainsString('"123"', $loggedRequest);
        self::assertStringNotContainsString($echo['headers']['x-header-signature'], $loggedRequest);
        self::assertStringContainsString('IVAN', $loggedRequest);
        self::assertStringContainsString('4111111111111111', $echo['body'], 'на шлюз тело уходит без маски');
    }

    public function testTokenCardAndCookieOfResponseNeverReachTheLog(): void
    {
        $logger = new RecordingLogger();

        $result = $this->createApiRequest()->setLogger($logger)->sendGetRequest('/token');

        $loggedResponse = json_encode($logger->records[1], JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('tok_secret_value', $loggedResponse);
        self::assertStringNotContainsString('5555555555554444', $loggedResponse);
        self::assertStringNotContainsString('server-secret', $loggedResponse);
        self::assertStringContainsString('MC', $loggedResponse);
        self::assertStringContainsString('tok_secret_value', $result['response'], 'вызывающий код получает ответ без маски');
    }

    public function testLongBodyIsTruncatedInLog(): void
    {
        $logger = new RecordingLogger();
        $apiRequest = $this->createApiRequest()->setLogger($logger)->setLogBodyMaxLength(20);

        $apiRequest->sendPostRequest('{"description":"' . str_repeat('я', 500) . '"}', '/echo');

        self::assertSame(20, mb_strpos($logger->records[0]['context']['body'], '…'));
    }

    public function testSensitiveFieldListCanBeReplaced(): void
    {
        $logger = new RecordingLogger();
        $apiRequest = $this->createApiRequest()->setLogger($logger)->setLogSensitiveFields(['owner']);

        $apiRequest->sendPostRequest(self::CARD_BODY, '/echo');

        self::assertStringNotContainsString('IVAN', $logger->records[0]['context']['body']);
    }

    public function testBodiesCanBeExcludedFromLog(): void
    {
        $logger = new RecordingLogger();

        $this->createApiRequest()->setLogger($logger)->setLogBodies(false)->sendPostRequest(self::CARD_BODY, '/echo');

        self::assertArrayNotHasKey('body', $logger->records[0]['context']);
        self::assertArrayNotHasKey('body', $logger->records[1]['context']);
    }

    public function testServerErrorIsLoggedAsError(): void
    {
        $logger = new RecordingLogger();

        $this->createApiRequest()->setLogger($logger)->sendGetRequest('/error500');

        self::assertSame('error', $logger->records[1]['level']);
        self::assertSame(500, $logger->records[1]['context']['http_code']);
    }

    public function testNonJsonResponseIsLoggedAsIs(): void
    {
        $logger = new RecordingLogger();

        $this->createApiRequest()->setLogger($logger)->sendGetRequest('/html');

        self::assertSame('<h1>Bad Gateway</h1>', $logger->records[1]['context']['body']);
    }

    public function testTransportFailureIsLoggedWithCurlErrorCode(): void
    {
        $logger = new RecordingLogger();
        $apiRequest = $this->createApiRequest()->setLogger($logger)->setCurlOption(CURLOPT_TIMEOUT, 1);

        try {
            $apiRequest->sendPostRequest('{"a":1}', '/slow');
            self::fail('Ожидалось исключение по таймауту');
        } catch (PaymentException $e) {
            $failure = $logger->records[1];
            self::assertSame('error', $failure['level']);
            self::assertSame(CURLE_OPERATION_TIMEDOUT, $failure['context']['curl_errno']);
            self::assertNotSame('', $failure['context']['curl_error']);
        }
    }

    public function testLegacyReportRequestWorksAndHidesUrlSignatureInLog(): void
    {
        $logger = new RecordingLogger();
        $apiRequest = $this->createApiRequest()->setLogger($logger)->setIdempotencyKey('key-1');
        $apiRequest->sendPostRequest('{"a":1}', '/echo');

        $echo = json_decode($apiRequest->sendGetReportRequest('2026-01-01', '2026-01-02'), true, 512, JSON_THROW_ON_ERROR);

        parse_str((string) parse_url($echo['uri'], PHP_URL_QUERY), $query);
        self::assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $query['signature']);
        self::assertArrayNotHasKey('x-header-signature', $echo['headers'], 'подпись прошлого запроса не должна уходить повторно');
        self::assertStringNotContainsString($query['signature'], $logger->records[2]['context']['url']);
        self::assertStringContainsString('signature=***', $logger->records[2]['context']['url']);
    }

    public function testFailingLoggerDoesNotBreakThePayment(): void
    {
        $logger = new class extends AbstractLogger {
            public function log($level, $message, array $context = []): void
            {
                throw new RuntimeException('log storage is down');
            }
        };

        $result = $this->createApiRequest()->setLogger($logger)->sendPostRequest('{"a":1}', '/echo');

        self::assertSame(200, $result['httpCode']);
    }

    public function testWithoutLoggerAndDebugModeNothingIsPrinted(): void
    {
        $this->expectOutputString('');

        $this->createApiRequest()->sendPostRequest(self::CARD_BODY, '/echo');
    }
}
