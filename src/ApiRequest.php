<?php

declare(strict_types=1);

namespace Ypmn;

use DateTime;
use DateTimeInterface;
use JsonException;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Класс отправки запроса к API
 */
class ApiRequest implements ApiRequestInterface
{
    public const AUTHORIZE_API = '/api/v4/payments/authorize';
    public const CAPTURE_API = '/api/v4/payments/capture';
    public const TOKEN_API = '/api/v4/token';
    public const REFUND_API = '/api/v4/payments/refund';
    public const STATUS_API = '/api/v4/payments/status';
    public const PAYOUT_CREATE_API = '/api/v4/payout';
    public const PAYOUT_GET_BALANCE_API = '/api/v4/payout/balance';
    public const REPORTS_ORDERS_API = '/reports/orders';
    public const SESSION_API = '/api/v4/payments/sessions';
    public const REPORT_CHART_API = '/api/v4/reports/chart';
    public const REPORT_GENERAL_API = '/api/v4/reports/general';
    public const REPORT_ORDERS_API_V4 = '/api/v4/reports/orders';
    public const REPORT_ORDER_DETAILS_API = '/api/v4/reports/order-details';
    public const PODELI_MERCHANT_REGISTRATION_API = '/api/v4/registration/merchant/podeli';
    public const QST_CREATE_API = '/api/v4/qst/create';
    public const QST_STATUS_API = '/api/v4/qst/status';
    public const QST_PRINT_API = '/api/v4/qst/print';
    public const QST_LIST_API = '/api/v4/qst/list';
    public const HOST = 'https://secure.ypmn.ru';
    public const SANDBOX_HOST = 'https://sandbox.ypmn.ru';
    public const LOCAL_HOST = 'http://127.0.0.1';

    /** @var int Время ожидания соединения по умолчанию, секунды */
    public const DEFAULT_CONNECT_TIMEOUT = 10;

    /** @var int Время ожидания ответа на GET-запрос по умолчанию, секунды */
    public const DEFAULT_GET_TIMEOUT = 30;

    /** @var int Время ожидания ответа на POST- и PUT-запрос по умолчанию, секунды */
    public const DEFAULT_POST_TIMEOUT = 60;

    /** @var int Наибольшая длина тела запроса и ответа в журнале по умолчанию, символы */
    public const DEFAULT_LOG_BODY_MAX_LENGTH = 10000;

    /** @var array Опции cURL по умолчанию; переопределяются через setCurlOption() */
    private const DEFAULT_CURL_OPTIONS = [
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CONNECTTIMEOUT => self::DEFAULT_CONNECT_TIMEOUT,
        CURLOPT_TIMEOUT => self::DEFAULT_GET_TIMEOUT,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ];

    /**
     * @var int[] Опции cURL, которыми управляет SDK: от них зависят подпись запроса и разбор ответа,
     *            поэтому переопределить их нельзя
     */
    private const PROTECTED_CURL_OPTIONS = [
        CURLOPT_URL,
        CURLOPT_CUSTOMREQUEST,
        CURLOPT_POSTFIELDS,
        CURLOPT_HTTPHEADER,
        CURLOPT_RETURNTRANSFER,
        CURLOPT_HEADER,
        CURLOPT_HEADERFUNCTION,
    ];

    /** @var MerchantInterface Мерчант, от имени которого отправляется запрос */
    private MerchantInterface $merchant;

    /** @var bool Режим Песочницы */
    private bool $sandboxModeIsOn = false;

    /** @var bool Режим отправки запросов на локальный хост */
    private bool $localModeIsOn = false;

    /** @var bool Режим Отладки (вывод системных сообщений) */
    private bool $debugModeIsOn = false;

    /** @var bool Отображать заголовки ответа в режим отладки */
    private bool $debugShowResponseHeaders = true;

    /** @var bool Формат результата в режиме отладки */
    private bool $jsonDebugResponse = true;

    /** @var string Хост для отправки запросов */
    private string $host = self::HOST;

    /** @var string Ключ идемпотентности */
    private string $idempotencyKey = "";

    /** @var array Пользовательские заголовки запроса */
    private array $headers = [];

    /** @var array Пользовательские опции cURL в формате [CURLOPT_* => значение] */
    private array $curlOptions = [];

    /** @var LoggerInterface|null Журнал запросов (PSR-3) */
    private ?LoggerInterface $logger = null;

    /** @var bool Записывать в журнал тела запросов и ответов */
    private bool $logBodies = true;

    /** @var string[] Поля JSON, значения которых скрываются в журнале */
    private array $logSensitiveFields = LogSanitizer::DEFAULT_SENSITIVE_FIELDS;

    /** @var int Наибольшая длина тела запроса и ответа в журнале, символы */
    private int $logBodyMaxLength = self::DEFAULT_LOG_BODY_MAX_LENGTH;

    /** @inheritdoc  */
    public function __construct(MerchantInterface $merchant)
    {
        $this->merchant = $merchant;
    }

    /** @inheritdoc */
    public function getIdempotencyKey(): string
    {
        return $this->idempotencyKey;
    }

    /** @inheritdoc */
    public function setIdempotencyKey(string $idempotencyKey): self
    {
        if (mb_strlen($idempotencyKey) <= 36) {
            $this->idempotencyKey = $idempotencyKey;

            return $this;
        } else {
            throw new PaymentException('Ключ идемпотентности должен быть не длинее 36 символов, подробнее: https://ypmn.ru/ru/documentation/#tag/idempotency');
        }
    }

    /** @inheritdoc  */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @inheritdoc  */
    public function setHeader(string $name, string $value): self
    {
        $name = trim($name);
        $value = trim($value);

        if (preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name) !== 1) {
            throw new PaymentException('Некорректное имя заголовка запроса');
        }

        if (preg_match('/[\r\n\0]/', $value) === 1) {
            throw new PaymentException('Значение заголовка ' . $name . ' не должно содержать переводов строки');
        }

        if ($value !== '') {
            $this->headers = self::withHeader($this->headers, $name, $value);
        }

        return $this;
    }

    /** @inheritdoc  */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->setHeader((string) $name, (string) $value);
        }

        return $this;
    }

    /** @inheritdoc  */
    public function removeHeader(string $name): self
    {
        $this->headers = self::withoutHeader($this->headers, trim($name));

        return $this;
    }

    /** @inheritdoc  */
    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    /** @inheritdoc  */
    public function setCurlOption(int $option, $value): self
    {
        if (in_array($option, self::PROTECTED_CURL_OPTIONS, true)) {
            throw new PaymentException(
                'Опцией cURL ' . $option . ' управляет SDK, переопределить её нельзя. '
                . 'Заголовки задаются через setHeader(), адрес -- через setHost()'
            );
        }

        $this->curlOptions[$option] = $value;

        return $this;
    }

    /** @inheritdoc  */
    public function setCurlOptions(array $options): self
    {
        foreach ($options as $option => $value) {
            if (!is_int($option)) {
                throw new PaymentException('Ключами массива опций cURL должны быть константы CURLOPT_*');
            }

            $this->setCurlOption($option, $value);
        }

        return $this;
    }

    /** @inheritdoc  */
    public function setLogger(?LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /** @inheritdoc  */
    public function setLogBodies(bool $logBodies = true): self
    {
        $this->logBodies = $logBodies;

        return $this;
    }

    /** @inheritdoc  */
    public function setLogSensitiveFields(array $fields): self
    {
        $this->logSensitiveFields = array_map('strval', array_values($fields));

        return $this;
    }

    /** @inheritdoc  */
    public function setLogBodyMaxLength(int $maxLength): self
    {
        if ($maxLength < 0) {
            throw new PaymentException('Наибольшая длина тела в журнале не может быть отрицательной');
        }

        $this->logBodyMaxLength = $maxLength;

        return $this;
    }

    /** @inheritdoc  */
    public function getHost(): string
    {
        return $this->host;
    }

    /** @inheritdoc  */
    public function setHost(string $host): self
    {
        if (filter_var($host, FILTER_VALIDATE_URL)) {
            $this->host = $host;

            return $this;
        } else {
            throw new PaymentException('Некорректный URL для отправки запросов');
        }
    }

    /** @deprecated старая версия */
    public function sendGetReportRequest(?string $startDate = null, ?string $endDate = null, ?array $orderStatus = null): string
    {
        //проверить даты
        if ($startDate !== null) {
            if (($startDate = strtotime($startDate)) === false) {
                throw new \Exception('Неверная дата для формирования запроса');
            } else {
                $startDate = date('Y-m-d', $startDate);
            }
        } else {
            $startDate = date('Y-m-d', strtotime('today'));
        }

        if ($endDate !== null) {
            if (($endDate = strtotime($endDate)) === false) {
                throw new \Exception('Неверная дата для формирования запроса');
            } else {
                $endDate = date('Y-m-d', $endDate);
            }
        } else {
            $endDate = date('Y-m-d', strtotime('tomorrow'));
        }

        $merchant = $this->merchant->getCode();
        $timeStamp = time();
        $parameters = compact('merchant', 'startDate', 'endDate', 'timeStamp');

        //сформировать URL
        $url = $this->getHost()
            . $this::REPORTS_ORDERS_API
            . '?'
            .  http_build_query($parameters)
            . '&signature='
            . $this->reportsSign($parameters);


        if ($this->getDebugMode()) {
            echo Std::alert([
                'text' => $url,
            ]);
        }

        // отправить запрос
        $date = (new DateTime())->format(DateTimeInterface::ATOM);

        $requestHeaders = $this->buildRequestHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Header-Date' => $date,
            'X-Header-Merchant' => $this->merchant->getCode(),
        ]);

        $result = $this->executeRequest('GET', $url, $requestHeaders, self::DEFAULT_CURL_OPTIONS);
        $response = $result['response'];
        $err = $result['error'];

        if ($this->getDebugMode()) {
            $this->echoDebugMessage('Ответ от ' . $this->getHost() . ':');
            $this->echoDebugMessage(Std::json_fix_cyr($response));

            if ($err) {
                $this->echoDebugMessage('Ошибка:');
                $this->echoDebugMessage($err);
            }
        }

        // вернуть результат
        return Std::json_fix_cyr($response);
    }

    private function buildReportsSourceString($parameters)
    {
        $hashString = '';

        foreach ($parameters as $currentData) {
            // метка времени приходит числом, а strlen() в строгом режиме типов принимает только строку
            $currentData = (string) $currentData;

            if (strlen($currentData) > 0) {
                $hashString .= strlen($currentData);
                $hashString .= $currentData;
            }
        }

        return $hashString;
    }

    /**
     * Расчет подписи для API v3
     * @param $parameters
     * @return string
     */
    private function reportsSign($parameters): string
    {
        $sourceString = $this->buildReportsSourceString($parameters);

        return hash_hmac('MD5', $sourceString, $this->merchant->getSecret());
    }

    /**
     * Отправка GET-запроса
     * @param string $api адрес API (URI)
     * @return array ответ сервера Ypmn
     * @throws PaymentException
     */
    public function sendGetRequest(string $api): array
    {
        $date = (new DateTime())->format(DateTimeInterface::ATOM);
        $requestHttpVerb = 'GET';

        $requestHeaders = $this->buildRequestHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Header-Date' => $date,
            'X-Header-Merchant' => $this->merchant->getCode(),
            'X-Header-Signature' => $this->getSignature(
                $this->merchant,
                $date,
                $this->getHost() . $api,
                $requestHttpVerb,
                md5('')
            ),
        ]);

        $result = $this->executeRequest(
            $requestHttpVerb,
            $this->getHost() . $api,
            $requestHeaders,
            self::DEFAULT_CURL_OPTIONS
        );
        $response = $result['response'];
        $err = $result['error'];
        $headers = $result['headers'];

        if (true === $this->getDebugMode()) {
            $this->echoDebugMessage('GET-Запрос к серверу Ypmn:');
            $this->echoDebugMessage($this->getHost() . $api);
            $this->echoDebugMessage('Ответ от сервера Ypmn:');
            if ($this->getJsonDebugResponse()) {
                $this->echoDebugMessage(json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $this->echoDebugMessage($response);
            }

            if ($this->getDebugShowResponseHeaders()) {
                $this->echoDebugMessage('Заголовки ответа от сервера Ypmn:');
                $this->echoDebugMessage(implode("\n", $headers));
            }

            if (mb_strlen($err) > 0) {
                $this->echoDebugMessage('Ошибка');
                echo '<br>Вы можете отправить запрос на поддержку на <a href="mailto:itsupport@ypmn.ru?subject=YPMN_Integration">itsupport@ypmn.ru</a>';
                echo '<br><a href="https://github.com/yourpayments/php-api-client/">Последняя версия примеров на Github</a>';
                echo '<br><a href="https://github.com/yourpayments/php-api-client/issues">Оставить заявку на улучшение</a>';
                echo '<br><a href="https://ypmn.ru/ru/contacts/">Контакты</a>';
            } else {
                $cpanel_url = 'https://' . ($this->getSandboxMode() ? 'sandbox' : 'secure' ) . '.ypmn.ru/cpanel/';

                if ($this->getSandboxMode()) {
                    echo Std::alert([
                        'type' => 'warning',
                        'text' => '
                            Внимание!
                            У вас настроен тестовый режим.
                            <br>Все запросы уходят на тестовый сервер <a href="' . $cpanel_url . '" class="alert-link">sandbox.ypmn.ru</a>
                            <br>
                            <br>
                            Когда закончите тестирование, закомментируйте или удалите строки кода:
                            <code class="d-block ml-2">
                                $apiRequest->setDebugMode(); // вывод отладки
                                <br>$apiRequest->setSandboxMode(); // тестовый сервер
                            </code>
                        ',
                    ]);
                }
            }
        }

        return $result;
    }

    /**
     * Функция-декоратор для отправки POST-запроса
     * @param string|JsonSerializable $data запрос
     * @param string $api адрес API (URI)
     * @param string|null $customErrorMessage Переопредеяемое пользователем сообщение
     *                                         при отсутствии ответа от сервера (опционально)
     * @return array ответ сервера Ypmn
     * @throws PaymentException
     */
    public function sendPostRequest($data, string $api, ?string $customErrorMessage = null): array
    {
        return $this->sendPostPutRequest($data, $api, 'POST', $customErrorMessage);
    }

    /**
     * Функция-декоратор для отправки PUT-запроса
     * @param string|JsonSerializable $data запрос
     * @param string $api адрес API (URI)
     * @param string|null $customErrorMessage Переопредеяемое пользователем сообщение
     *                                         при отсутствии ответа от сервера (опционально)
     * @return array ответ сервера Ypmn
     * @throws PaymentException
     */
    public function sendPutRequest($data, string $api, ?string $customErrorMessage = null): array
    {
        return $this->sendPostPutRequest($data, $api, 'PUT', $customErrorMessage);
    }

    /**
     * Отправка POST и PUT запросов
     * @param string|JsonSerializable $data запрос
     * @param string $api адрес API (URI)
     * @param string $method HTTP-метод (POST | PUT)
     * @param string|null $customErrorMessage Переопредеяемое пользователем сообщение
     *                                        при отсутствии ответа от сервера (опционально)
     * @return array ответ сервера Ypmn
     * @throws PaymentException
     */
    private function sendPostPutRequest(
        $data,
        string $api,
        string $method,
        ?string $customErrorMessage = null
    ): array {
        if ($data instanceof JsonSerializable) {
            $encodedJsonData = $data->jsonSerialize();
        } elseif (is_string($data)) {
            if (json_decode($data) !== null) {
                $encodedJsonData = $data;
            } else {
                throw new PaymentException('Incorrect request body type');
            }
        } else {
            throw new PaymentException('Incorrect request body JSON');
        }

        $encodedJsonDataHash = md5($encodedJsonData);

        $date = (new DateTime())->format(DateTimeInterface::ATOM);

        $useragent = "SDK_PHP_" . @PHP_VERSION;
        $referer =  @$_SERVER['HTTP_HOST'] ?? @$_SERVER['SERVER_NAME'] ?? "" . @$_SERVER['REQUEST_URI'] ?? "";

        $systemHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Header-Date' => $date,
            'X-Header-Merchant' => $this->merchant->getCode(),
            'X-Header-Signature' => $this->getSignature(
                $this->merchant,
                $date,
                $this->getHost() . $api,
                $method,
                $encodedJsonDataHash
            ),
        ];

        if ($this->getIdempotencyKey()) {
            $systemHeaders['X-Header-Idempotency-Key'] = $this->getIdempotencyKey();
        }

        $defaultCurlOptions = [
            CURLOPT_USERAGENT => strip_tags($useragent),
            CURLOPT_REFERER => strip_tags($referer),
            CURLOPT_TIMEOUT => self::DEFAULT_POST_TIMEOUT,
        ] + self::DEFAULT_CURL_OPTIONS;

        $result = $this->executeRequest(
            $method,
            $this->getHost() . $api,
            $this->buildRequestHeaders($systemHeaders),
            $defaultCurlOptions,
            $encodedJsonData
        );
        $response = $result['response'];
        $err = $result['error'];
        $headers = $result['headers'];

        if (true === $this->getDebugMode()) {
            $this->echoDebugMessage("$method-Запрос к серверу Ypmn:");
            $this->echoDebugMessage($encodedJsonData);
            $this->echoDebugMessage('Ответ от сервера Ypmn:');

            try {
                $this->echoDebugMessage(
                    json_encode(
                            json_decode($response, false, 16, JSON_THROW_ON_ERROR),
                            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                    )
                );
            } catch (JsonException $e) {
                $this->echoDebugMessage('Ошибка ответа: ');
                $this->echoDebugMessage($e->getMessage());
                $this->echoDebugMessage('Содержимо ответа:');
                $this->echoDebugMessage(htmlspecialchars($response));
            }

            if ($this->getDebugShowResponseHeaders()) {
                $this->echoDebugMessage('Заголовки ответа:');
                $this->echoDebugMessage(implode("\n", $headers));
            }

            if (mb_strlen($err) > 0) {
                $this->echoDebugMessage('Ошибка');
                $this->echoDebugMessage($encodedJsonData);

                echo '<br>Вы можете отправить запрос на поддержку на <a href="mailto:itsupport@ypmn.ru?subject=YPMN_Integration">itsupport@ypmn.ru</a>';
                echo '<br><a href="https://github.com/yourpayments/php-api-client/">Последняя версия примеров на Github</a>';
                echo '<br><a href="https://github.com/yourpayments/php-api-client/issues">Оставить заявку на улучшение</a>';
                echo '<br><a href="https://ypmn.ru/ru/contacts/">Контакты</a>';
            } else {
                $cpanel_url = 'https://' . ($this->getSandboxMode() ? 'sandbox' : 'secure' ) . '.ypmn.ru/cpanel/';

                if ($this->getSandboxMode()) {
                    echo Std::alert([
                        'type' => 'warning',
                        'text' => '
                            Внимание!
                            У вас настроен тестовый режим.
                            <br>Все запросы уходят на тестовый сервер <a href="' . $cpanel_url . '" class="alert-link">sandbox.ypmn.ru</a>
                            <br>
                            <br>
                            Когда закончите тестирование, закомментируйте или удалите строки кода:
                            <code class="d-block ml-2">
                                $apiRequest->setDebugMode(); // вывод отладки
                                <br>$apiRequest->setSandboxMode(); // тестовый сервер
                            </code>
                        ',
                    ]);
                }
            }
        }

        if (mb_strlen($err) > 0) {
            throw new PaymentException($err);
        }

        if ($response == null || strlen($response) === 0) {
            throw new PaymentException(
                $customErrorMessage ?? 'Вы можете попробовать другой способ оплаты, либо свяжитесь с продавцом.'
            );
        }

        return $result;
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendSessionRequest(SessionRequest $sessionRequest): array
    {
        return $this->sendPostRequest($sessionRequest, self::SESSION_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendAuthRequest(PaymentInterface $payment): array
    {
        return $this->sendPostRequest($payment, self::AUTHORIZE_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendCaptureRequest(CaptureInterface $capture): array
    {
        return $this->sendPostRequest($capture, self::CAPTURE_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendRefundRequest(RefundInterface $refund): array
    {
        return $this->sendPostRequest($refund, self::REFUND_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendStatusRequest(string $merchantPaymentReference): array
    {
        $responseData = $this->sendGetRequest(self::STATUS_API . '/' . $merchantPaymentReference);

        if (mb_strlen($responseData['error']) > 0) {
            throw new PaymentException($responseData['error']);
        }

        if ($responseData['response'] == null || strlen($responseData['response']) === 0) {
            throw new PaymentException('Вы можете попробовать другой способ оплаты, либо свяжитесь с продавцом.');
        }

        return $responseData;
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendTokenCreationRequest(PaymentReference $payuPaymentReference): array
    {
        return $this->sendPostRequest($payuPaymentReference, self::TOKEN_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendTokenPaymentRequest(MerchantToken $tokenHash): array
    {
        return $this->sendPostRequest($tokenHash, self::AUTHORIZE_API);
    }

    /**
     * @throws PaymentException
     */
    public function sendPayoutCreateRequest(PayoutInterface $payout): array
    {
        return $this->sendPostRequest($payout, self::PAYOUT_CREATE_API);
    }

    /**
     * @inheritdoc
     * @throws PaymentException
     */
    public function sendPayoutGetBalanceRequest(array $params = []): array
    {
        $url = self::PAYOUT_GET_BALANCE_API;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $responseData = $this->sendGetRequest($url);

        if (mb_strlen($responseData['error']) > 0) {
            throw new PaymentException($responseData['error']);
        }

        if ($responseData['response'] == null || strlen($responseData['response']) === 0) {
            throw new PaymentException('Непредвиденная ошибка!.');
        }

        return $responseData;
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendReportChartRequest(array $params): array
    {
        $this->setJsonDebugResponse(false);
        return $this->sendGetRequest(self::REPORT_CHART_API . '/?' . http_build_query($params));
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendReportChartUpdateRequest(array $params): array
    {
        $getParams = [
            'startDate' => $_GET['startDate'],
            'endDate' => $_GET['endDate'],
            'status' => $_GET['status'],
            'type' => $_GET['type'],
            'periodLength' => $_GET['periodLength'],
            'jsonForUpdate' => 'true'
        ];

        $params = array_merge($getParams, $params);

        return $this->sendGetRequest(self::REPORT_CHART_API . '/?' . http_build_query($params));
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendReportGeneralRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_GENERAL_API . '/?' . http_build_query($params));
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendReportOrderRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_ORDERS_API_V4 . '/?' . http_build_query($params));
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendReportOrderDetailsRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_ORDER_DETAILS_API . '/?' . http_build_query($params));
    }

    /**
     * Выполнить HTTP-запрос: единая точка, где применяются опции cURL и ведётся журнал
     * @param string $method HTTP-метод
     * @param string $url полный адрес запроса
     * @param array $headers заголовки запроса в формате ["Header-Name" => "value"]
     * @param array $defaultCurlOptions опции cURL по умолчанию для этого вида запроса
     * @param string|null $body тело запроса
     * @return array{response: string|false, error: string, httpCode: int, headers: string[]}
     * @throws PaymentException
     */
    private function executeRequest(
        string $method,
        string $url,
        array $headers,
        array $defaultCurlOptions,
        ?string $body = null
    ): array {
        $responseHeaders = [];
        $requiredCurlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => self::formatHeadersForCurl($headers),
            CURLOPT_HEADERFUNCTION => static function ($curl, string $header) use (&$responseHeaders): int {
                if (strlen(trim($header)) > 0) {
                    $responseHeaders[] = trim($header);
                }

                return strlen($header);
            },
        ];

        if ($body !== null) {
            $requiredCurlOptions[CURLOPT_POSTFIELDS] = $body;
        }

        // оператор «+» сохраняет числовые ключи и оставляет значение из левого массива:
        // обязательные опции важнее пользовательских, пользовательские важнее опций по умолчанию
        $curlOptions = $requiredCurlOptions + $this->curlOptions + $defaultCurlOptions;

        // идентификатор нужен только журналу: связывает записи о запросе и об ответе
        $requestId = ($this->logger === null) ? '' : bin2hex(random_bytes(8));
        $this->logRequest($requestId, $method, $url, $headers, $body);

        $curl = curl_init();

        try {
            $isConfigured = curl_setopt_array($curl, $curlOptions);
        } catch (Throwable $e) {
            throw new PaymentException('Некорректные опции cURL: ' . $e->getMessage(), 0, $e);
        }

        if ($isConfigured === false) {
            throw new PaymentException('Не удалось применить опции cURL: ' . curl_error($curl));
        }

        $startedAt = microtime(true);
        $response = curl_exec($curl);
        $durationMs = round((microtime(true) - $startedAt) * 1000, 1);
        $error = curl_error($curl);
        $errno = curl_errno($curl);
        $info = curl_getinfo($curl);

        if (PHP_VERSION_ID < 80000) {
            curl_close($curl);
        }

        $httpCode = (int) ($info['http_code'] ?? 0);

        $this->logResponse($requestId, $method, $url, $response, $responseHeaders, [
            'http_code' => $httpCode,
            'duration_ms' => $durationMs,
            'connect_time_ms' => round((float) ($info['connect_time'] ?? 0) * 1000, 1),
            'primary_ip' => (string) ($info['primary_ip'] ?? ''),
            'curl_errno' => $errno,
            'curl_error' => $error,
        ]);

        return ['response' => $response, 'error' => $error, 'httpCode' => $httpCode, 'headers' => $responseHeaders];
    }

    /**
     * Записать в журнал исходящий запрос
     * @param string $requestId идентификатор, общий для записей о запросе и об ответе
     * @param string $method HTTP-метод
     * @param string $url полный адрес запроса
     * @param array $headers заголовки запроса
     * @param string|null $body тело запроса
     * @return void
     */
    private function logRequest(string $requestId, string $method, string $url, array $headers, ?string $body): void
    {
        if ($this->logger === null) {
            return;
        }

        $context = [
            'request_id' => $requestId,
            'method' => $method,
            'url' => LogSanitizer::maskUrl($url),
            'headers' => LogSanitizer::maskHeaders($headers),
        ];

        if ($this->logBodies && $body !== null) {
            $context['body'] = LogSanitizer::maskBody($body, $this->logSensitiveFields, $this->logBodyMaxLength);
        }

        $this->writeLog('debug', 'YPMN API request', $context);
    }

    /**
     * Записать в журнал ответ сервера или ошибку соединения.
     * Уровень записи: error -- ошибка соединения или ответ 5xx, warning -- ответ 4xx, info -- остальное
     * @param string $requestId идентификатор, общий для записей о запросе и об ответе
     * @param string $method HTTP-метод
     * @param string $url полный адрес запроса
     * @param string|false $response тело ответа
     * @param string[] $responseHeaders заголовки ответа
     * @param array $metrics код ответа, длительность и сведения об ошибке cURL
     * @return void
     */
    private function logResponse(
        string $requestId,
        string $method,
        string $url,
        $response,
        array $responseHeaders,
        array $metrics
    ): void {
        if ($this->logger === null) {
            return;
        }

        $context = [
            'request_id' => $requestId,
            'method' => $method,
            'url' => LogSanitizer::maskUrl($url),
        ] + $metrics + [
            'headers' => LogSanitizer::maskHeaderLines($responseHeaders),
        ];

        if ($this->logBodies && is_string($response)) {
            $context['body'] = LogSanitizer::maskBody($response, $this->logSensitiveFields, $this->logBodyMaxLength);
        }

        if ($metrics['curl_errno'] !== 0) {
            $this->writeLog('error', 'YPMN API request failed', $context);
        } elseif ($metrics['http_code'] >= 500) {
            $this->writeLog('error', 'YPMN API response', $context);
        } elseif ($metrics['http_code'] >= 400) {
            $this->writeLog('warning', 'YPMN API response', $context);
        } else {
            $this->writeLog('info', 'YPMN API response', $context);
        }
    }

    /**
     * @param string $level уровень записи PSR-3
     * @param string $message сообщение
     * @param array $context контекст
     * @return void
     */
    private function writeLog(string $level, string $message, array $context): void
    {
        try {
            $this->logger->log($level, $message, $context);
        } catch (Throwable $e) {
            // Сбой журнала намеренно не пробрасывается: запрос к шлюзу уже отправлен или будет отправлен,
            // и исключение здесь выглядело бы для вызывающего кода как неуспешный платёж.
        }
    }

    /**
     * Собрать заголовки запроса: пользовательские и поверх них системные.
     * Системные заголовки не сохраняются в объекте, поэтому не попадают в следующие запросы.
     * @param array $systemHeaders заголовки, которые выставляет SDK
     * @return array заголовки в формате ["Header-Name" => "value"]
     */
    private function buildRequestHeaders(array $systemHeaders): array
    {
        $headers = $this->headers;

        foreach ($systemHeaders as $name => $value) {
            $headers = self::withHeader($headers, $name, $value);
        }

        return $headers;
    }

    /**
     * Вернуть справочник заголовков с добавленным заголовком; имя сравнивается без учёта регистра
     * @param array $headers заголовки в формате ["Header-Name" => "value"]
     * @param string $name имя заголовка
     * @param string $value значение заголовка
     * @return array
     */
    private static function withHeader(array $headers, string $name, string $value): array
    {
        $headers = self::withoutHeader($headers, $name);
        $headers[$name] = $value;

        return $headers;
    }

    /**
     * Вернуть справочник заголовков без указанного заголовка; имя сравнивается без учёта регистра
     * @param array $headers заголовки в формате ["Header-Name" => "value"]
     * @param string $name имя заголовка
     * @return array
     */
    private static function withoutHeader(array $headers, string $name): array
    {
        foreach (array_keys($headers) as $existingName) {
            if (strcasecmp((string) $existingName, $name) === 0) {
                unset($headers[$existingName]);
            }
        }

        return $headers;
    }

    /**
     * Привести справочник заголовков (key => value) к формату CURLOPT_HTTPHEADER
     * @param array $headers заголовки в формате ["Header-Name" => "value"]
     * @return string[] массив строк вида ["Header-Name: value", ...]
     */
    private static function formatHeadersForCurl(array $headers): array
    {
        $curlHeaders = [];

        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }

        return $curlHeaders;
    }

    /**
     * Подпись запроса
     * @param MerchantInterface $merchant Мерчант
     * @param string $date Дата
     * @param string $url адрес отправки запроса
     * @param string $httpMethod HTTP
     * @param string $bodyHash md5-хэш запроса
     * @return string подпись
     * @throws PaymentException
     */
    private function getSignature(MerchantInterface $merchant, string $date, string $url, string $httpMethod, string $bodyHash): string
    {
        if (strlen($merchant->getCode()) < 2) {
            throw new PaymentException('YPMN-001: No Merchant Code');
        }

        $urlParts = parse_url($url);
        $urlHashableParts = $httpMethod . $urlParts['path'];
        $this->echoDebugMessage($urlParts);

        if (isset($urlParts['query'])) {
            $urlHashableParts .= $urlParts['query'];
        }
        $hashableString = $merchant->getCode() . $date . $urlHashableParts . $bodyHash;

        return hash_hmac('sha256', $hashableString, $merchant->getSecret());
    }

    /** @inheritdoc  */
    public function getSandboxMode(): bool
    {
        return $this->sandboxModeIsOn;
    }

    /** @inheritdoc  */
    public function setSandboxMode(bool $sandboxModeIsOn = true): self
    {
        if ($sandboxModeIsOn) {
            $this->setLocalMode(false);
            $this->host = self::SANDBOX_HOST;
        }
        $this->sandboxModeIsOn = $sandboxModeIsOn;

        return $this;
    }

    public function getLocalMode(): bool
    {
        return $this->localModeIsOn;
    }

    public function setLocalMode(bool $localModeIsOn = true): self
    {
        if ($localModeIsOn) {
            $this->setSandboxMode(false);
        }
        $this->localModeIsOn = $localModeIsOn;
        $this->host = self::LOCAL_HOST;

        return $this;
    }

    /** @inheritdoc  */
    public function getDebugMode(): bool
    {
        return $this->debugModeIsOn;
    }

    /** @inheritdoc  */
    public function setDebugMode(bool $debugModeIsOn = true): self
    {
        $this->debugModeIsOn = $debugModeIsOn;

        return $this;
    }

    /** @inheritdoc  */
    public function setJsonDebugResponse(bool $jsonDebugResponse): self
    {
        $this->jsonDebugResponse = $jsonDebugResponse;

        return $this;
    }

    /** @inheritdoc  */
    public function getJsonDebugResponse(): bool
    {
        return $this->jsonDebugResponse;
    }

    /**
     * Вывод отладочного сообщения
     * @param $mixedInput
     * @return void
     */
    private function echoDebugMessage($mixedInput): void
    {
        if ($this->getDebugMode()) {
            echo '
                <pre
                    class="w-100 d-block"
                    style="
                        background: aliceblue;
                        color: black;
                        padding: 4px;
                        border-radius: 4px;
                        border: 1px solid green;
                        white-space: pre-wrap;
                    "
                >' . htmlspecialchars(print_r($mixedInput, true)) . '</pre>';
        }
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendPodeliRegistrationMerchantRequest(PodeliMerchant $merchant): array
    {
        return $this->sendPostRequest($merchant, self::PODELI_MERCHANT_REGISTRATION_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendQstCreateRequest(QstInterface $qst): array
    {
        return $this->sendPostRequest($qst, self::QST_CREATE_API);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendQstStatusRequest(int $qstId): array
    {
        return $this->sendGetRequest(self::QST_STATUS_API . '/' . $qstId);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendQstPrintRequest(int $qstId): array
    {
        return $this->sendGetRequest(self::QST_PRINT_API . '/' . $qstId);
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendQstListRequest(): array
    {
        return $this->sendGetRequest(self::QST_LIST_API);
    }

    /** @inheritdoc  */
    public function getDebugShowResponseHeaders(): bool
    {
        return $this->debugShowResponseHeaders;
    }

    /** @inheritdoc  */
    public function setDebugShowResponseHeaders(bool $debugShowResponseHeaders = true): self
    {
        $this->debugShowResponseHeaders = $debugShowResponseHeaders;
        return $this;
    }
}
