<?php

declare(strict_types=1);

namespace Ypmn\Tests;

use Ypmn\PaymentException;

final class CurlOptionsTest extends GatewayTestCase
{
    public function testSendsDefaultUserAgentWhenNothingIsOverridden(): void
    {
        $echo = $this->decodeEcho($this->createApiRequest()->sendPostRequest('{"a":1}', '/echo'));

        self::assertSame('SDK_PHP_' . PHP_VERSION, $echo['headers']['user-agent']);
    }

    public function testUserCurlOptionOverridesSdkDefault(): void
    {
        $apiRequest = $this->createApiRequest()->setCurlOption(CURLOPT_USERAGENT, 'my-shop/1.0');

        $echo = $this->decodeEcho($apiRequest->sendPostRequest('{"a":1}', '/echo'));

        self::assertSame('my-shop/1.0', $echo['headers']['user-agent']);
    }

    public function testUserTimeoutAbortsSlowPostRequest(): void
    {
        $apiRequest = $this->createApiRequest()->setCurlOptions([CURLOPT_TIMEOUT => 1]);
        $startedAt = microtime(true);

        try {
            $apiRequest->sendPostRequest('{"a":1}', '/slow');
            self::fail('Ожидалось исключение по таймауту');
        } catch (PaymentException $e) {
            self::assertLessThan(2.5, microtime(true) - $startedAt);
        }
    }

    public function testUserTimeoutAbortsSlowGetRequest(): void
    {
        $result = $this->createApiRequest()->setCurlOption(CURLOPT_TIMEOUT, 1)->sendGetRequest('/slow');

        self::assertNotSame('', $result['error']);
    }

    public function testSetCurlOptionsMergesWithPreviouslySetOptions(): void
    {
        $apiRequest = $this->createApiRequest()
            ->setCurlOption(CURLOPT_CONNECTTIMEOUT, 3)
            ->setCurlOptions([CURLOPT_TIMEOUT => 7]);

        self::assertSame([CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 7], $apiRequest->getCurlOptions());
    }

    /** @dataProvider protectedOptions */
    public function testRejectsOptionsTheSdkControlsItself(int $option): void
    {
        $this->expectException(PaymentException::class);

        $this->createApiRequest()->setCurlOption($option, 'x');
    }

    public function protectedOptions(): array
    {
        return [
            'url' => [CURLOPT_URL],
            'body' => [CURLOPT_POSTFIELDS],
            'headers' => [CURLOPT_HTTPHEADER],
            'method' => [CURLOPT_CUSTOMREQUEST],
        ];
    }

    /**
     * PHP 7.4 молча пропускает неизвестную опцию cURL, с PHP 8.0 это ошибка
     * @requires PHP >= 8.0
     */
    public function testUnknownCurlOptionIsReportedAsPaymentException(): void
    {
        $apiRequest = $this->createApiRequest()->setCurlOption(999999, 1);

        $this->expectException(PaymentException::class);

        $apiRequest->sendGetRequest('/echo');
    }

    public function testResultExposesHttpCodeAndResponseHeaders(): void
    {
        $result = $this->createApiRequest()->sendGetRequest('/error500');

        self::assertSame(500, $result['httpCode']);
        self::assertContains('X-Test-Response: yes', $result['headers']);
    }
}
