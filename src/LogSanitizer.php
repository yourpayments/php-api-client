<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Подготовка данных запроса и ответа к записи в журнал:
 * скрывает платёжные данные и подписи, ограничивает размер тела.
 */
final class LogSanitizer
{
    /** @var string Чем заменяется скрытое значение */
    public const MASK = '***';

    /** @var string[] Поля JSON, значения которых скрываются по умолчанию (без учёта регистра) */
    public const DEFAULT_SENSITIVE_FIELDS = [
        'number',
        'cardNumber',
        'pan',
        'cvv',
        'expiryMonth',
        'expiryYear',
        'token',
        'tokenHash',
    ];

    /** @var string[] Заголовки, значения которых скрываются (в нижнем регистре) */
    private const SENSITIVE_HEADERS = [
        'x-header-signature',
        'authorization',
        'proxy-authorization',
        'cookie',
        'set-cookie',
    ];

    /** @var string[] Параметры адреса, значения которых скрываются */
    private const SENSITIVE_QUERY_PARAMETERS = ['signature'];

    /**
     * Скрыть значения чувствительных параметров в адресе запроса
     * @param string $url адрес запроса
     * @return string
     */
    public static function maskUrl(string $url): string
    {
        $names = implode('|', array_map('preg_quote', self::SENSITIVE_QUERY_PARAMETERS));

        return (string) preg_replace('/([?&](?:' . $names . ')=)[^&#]*/i', '$1' . self::MASK, $url);
    }

    /**
     * Скрыть значения чувствительных заголовков запроса
     * @param array<string, string> $headers заголовки в формате ["Header-Name" => "value"]
     * @return array<string, string>
     */
    public static function maskHeaders(array $headers): array
    {
        $masked = [];

        foreach ($headers as $name => $value) {
            $isSensitive = in_array(strtolower((string) $name), self::SENSITIVE_HEADERS, true);
            $masked[$name] = $isSensitive ? self::MASK : $value;
        }

        return $masked;
    }

    /**
     * Скрыть значения чувствительных заголовков ответа
     * @param string[] $headerLines строки вида "Header-Name: value"
     * @return string[]
     */
    public static function maskHeaderLines(array $headerLines): array
    {
        $masked = [];

        foreach ($headerLines as $line) {
            $name = strtolower(trim((string) strstr($line, ':', true)));
            $isSensitive = in_array($name, self::SENSITIVE_HEADERS, true);
            $masked[] = $isSensitive ? strstr($line, ':', true) . ': ' . self::MASK : $line;
        }

        return $masked;
    }

    /**
     * Подготовить тело запроса или ответа: скрыть чувствительные поля JSON и обрезать по длине.
     * Тело, которое не является JSON (например, HTML-страница ошибки), только обрезается.
     * @param string $body тело запроса или ответа
     * @param string[] $sensitiveFields имена полей JSON, значения которых нужно скрыть
     * @param int $maxLength наибольшая длина результата в символах
     * @return string
     */
    public static function maskBody(string $body, array $sensitiveFields, int $maxLength): string
    {
        $decoded = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);

        if (is_array($decoded)) {
            $encoded = json_encode(
                self::maskFields($decoded, array_map('strtolower', $sensitiveFields)),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );
            $body = ($encoded === false) ? self::MASK : $encoded;
        }

        if (mb_strlen($body) <= $maxLength) {
            return $body;
        }

        return mb_substr($body, 0, $maxLength) . '… [обрезано, всего символов: ' . mb_strlen($body) . ']';
    }

    /**
     * @param array $data разобранный JSON
     * @param string[] $sensitiveFields имена полей в нижнем регистре
     * @return array
     */
    private static function maskFields(array $data, array $sensitiveFields): array
    {
        $masked = [];

        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $sensitiveFields, true)) {
                $masked[$key] = self::MASK;
            } elseif (is_array($value)) {
                $masked[$key] = self::maskFields($value, $sensitiveFields);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }
}
