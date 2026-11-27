<?php

declare(strict_types=1);

namespace Ypmn\Tests;

use Ypmn\PaymentException;

final class HeadersTest extends GatewayTestCase
{
    public function testCustomHeaderIsSentWithGetAndPost(): void
    {
        $apiRequest = $this->createApiRequest()->setHeader('X-Request-Id', 'abc-123');

        $get = $this->decodeEcho($apiRequest->sendGetRequest('/echo'));
        $post = $this->decodeEcho($apiRequest->sendPostRequest('{"a":1}', '/echo'));

        self::assertSame('abc-123', $get['headers']['x-request-id']);
        self::assertSame('abc-123', $post['headers']['x-request-id']);
    }

    public function testSetHeadersAddsSeveralHeadersAtOnce(): void
    {
        $apiRequest = $this->createApiRequest()->setHeaders(['X-One' => '1', 'X-Two' => '2']);

        $echo = $this->decodeEcho($apiRequest->sendGetRequest('/echo'));

        self::assertSame('1', $echo['headers']['x-one']);
        self::assertSame('2', $echo['headers']['x-two']);
    }

    public function testHeaderValueZeroIsNotDropped(): void
    {
        $echo = $this->decodeEcho($this->createApiRequest()->setHeader('X-Retry-Count', '0')->sendGetRequest('/echo'));

        self::assertSame('0', $echo['headers']['x-retry-count']);
    }

    public function testHeaderNameIsCaseInsensitive(): void
    {
        $apiRequest = $this->createApiRequest()->setHeader('x-trace', 'old')->setHeader('X-Trace', 'new');

        self::assertSame(['X-Trace' => 'new'], $apiRequest->getHeaders());
    }

    public function testRemoveHeaderStopsSendingIt(): void
    {
        $apiRequest = $this->createApiRequest()->setHeader('X-Trace', '1')->removeHeader('x-trace');

        $echo = $this->decodeEcho($apiRequest->sendGetRequest('/echo'));

        self::assertArrayNotHasKey('x-trace', $echo['headers']);
    }

    /** @dataProvider injectionAttempts */
    public function testRejectsHeaderInjection(string $name, string $value): void
    {
        $this->expectException(PaymentException::class);

        $this->createApiRequest()->setHeader($name, $value);
    }

    public function injectionAttempts(): array
    {
        return [
            'crlf in value' => ['X-Trace', "1\r\nX-Header-Merchant: other"],
            'lf in value' => ['X-Trace', "1\nX-Injected: 1"],
            'colon in name' => ['X-Trace: 1', 'x'],
            'space in name' => ['X Trace', 'x'],
        ];
    }

    public function testCustomHeaderCannotReplaceSignature(): void
    {
        $apiRequest = $this->createApiRequest()->setHeader('x-header-signature', 'forged');

        $echo = $this->decodeEcho($apiRequest->sendGetRequest('/echo'));

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $echo['headers']['x-header-signature']);
    }

    public function testIdempotencyKeyOfPostDoesNotLeakIntoNextGet(): void
    {
        $apiRequest = $this->createApiRequest()->setIdempotencyKey('key-1');

        $post = $this->decodeEcho($apiRequest->sendPostRequest('{"a":1}', '/echo'));
        $get = $this->decodeEcho($apiRequest->sendGetRequest('/echo'));

        self::assertSame('key-1', $post['headers']['x-header-idempotency-key']);
        self::assertArrayNotHasKey('x-header-idempotency-key', $get['headers']);
    }

    public function testSendingRequestDoesNotChangeUserHeaders(): void
    {
        $apiRequest = $this->createApiRequest()->setHeader('X-Trace', '1');

        $apiRequest->sendGetRequest('/echo');

        self::assertSame(['X-Trace' => '1'], $apiRequest->getHeaders());
    }
}
