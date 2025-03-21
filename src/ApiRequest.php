<?php declare(strict_types=1);

namespace Ypmn;

use DateTime;
use DateTimeInterface;
use JsonSerializable;

/**
 * Класс отправки запроса к API
 */
class ApiRequest implements ApiRequestInterface
{
    const AUTHORIZE_API = '/api/v4/payments/authorize';
    const CAPTURE_API = '/api/v4/payments/capture';
    const TOKEN_API = '/api/v4/token';
    const REFUND_API = '/api/v4/payments/refund';
    const STATUS_API = '/api/v4/payments/status';
    const PAYOUT_CREATE_API = '/api/v4/payout';
    const REPORTS_ORDERS_API = '/reports/orders';
    const SESSION_API = '/api/v4/payments/sessions';
    const REPORT_CHART_API = '/api/v4/reports/chart';
    const REPORT_GENERAL_API = '/api/v4/reports/general';
    const REPORT_ORDERS_API_V4 = '/api/v4/reports/orders';
    const REPORT_ORDER_DETAILS_API = '/api/v4/reports/order-details';
    const PODELI_MERCHANT_REGISTRATION_API = '/api/v4/registration/merchant/podeli';
    const QST_CREATE_API = '/api/v4/qst/create';
    const QST_STATUS_API = '/api/v4/qst/status';
    const QST_PRINT_API = '/api/v4/qst/print';
    const QST_LIST_API = '/api/v4/qst/list';
    const HOST = 'https://secure.ypmn.ru';
    const SANDBOX_HOST = 'https://sandbox.ypmn.ru';
    const LOCAL_HOST = 'http://127.0.0.1';

    /** @var MerchantInterface Мерчант, от имени которого отправляется запрос */
    private MerchantInterface $merchant;

    /** @var bool Режим Песочницы (тестовая панель Ypmn) */
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
    public function getHost() : string
    {
        return $this->host;
    }

    /** @inheritdoc  */
    public function setHost(string $host) : self
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
        $curl = curl_init();
        $requestHttpVerb = 'GET';

        $date = (new DateTime())->format(DateTimeInterface::ATOM);
        $setopt_array = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestHttpVerb,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Header-Date: ' . $date,
//                'X-Header-Merchant: ' . $this->merchant->getCode()
            ]
        ];

        curl_setopt_array($curl, $setopt_array);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

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
//            if (is_array($currentData)) {
//                //TODO
//                $currentData = '';
//            }

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
    private function reportsSign($parameters)
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
    private function sendGetRequest(string $api): array
    {
        $curl = curl_init();
        $date = (new DateTime())->format(DateTimeInterface::ATOM);
        $requestHttpVerb = 'GET';

        $setopt_array = [
            CURLOPT_URL => $this->getHost() . $api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestHttpVerb,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Header-Date: ' . $date,
                'X-Header-Merchant: ' . $this->merchant->getCode(),
                'X-Header-Signature:' . $this->getSignature(
                    $this->merchant,
                    $date,
                    $this->getHost() . $api,
                    $requestHttpVerb,
                    md5(''),
                )
            ]
        ];

        $headers = [];

        if ($this->getDebugShowResponseHeaders()) {
            $this->addCurlOptHeaderFunction($setopt_array, $headers);
        }

        curl_setopt_array($curl, $setopt_array);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if (true === $this->getDebugMode()) {
            $this->echoDebugMessage('GET-Запрос к серверу Ypmn:');
            $this->echoDebugMessage($this->getHost() . $api);
            $this->echoDebugMessage('Ответ от сервера Ypmn:');
            if ($this->getJsonDebugResponse()) {
                $this->echoDebugMessage(json_encode(json_decode($response), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
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
                $cpanel_url = 'https://' . ($this->getSandboxMode() ? 'sandbox' : 'secure' ). '.ypmn.ru/cpanel/';

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

        return ['response' => $response, 'error' => $err];
    }

    /**
     * Отправка POST-запроса
     * @param string|JsonSerializable $data запрос
     * @param string $api адрес API (URI)
     * @return array ответ сервера Ypmn
     * @throws PaymentException
     */
    public function sendPostRequest($data, string $api): array
    {
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

        $curl = curl_init();
        $date = (new DateTime())->format(DateTimeInterface::ATOM);
        $requestHttpVerb = 'POST';

        $setOptArray = [
            CURLOPT_URL => $this->getHost() . $api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestHttpVerb,
            CURLOPT_POSTFIELDS => $encodedJsonData,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Header-Date: ' . $date,
                'X-Header-Merchant: ' . $this->merchant->getCode(),
                'X-Header-Signature:' . $this->getSignature(
                    $this->merchant,
                    $date,
                    $this->getHost() . $api,
                    $requestHttpVerb,
                    $encodedJsonDataHash
                )
            ]
        ];

        $headers = [];

        if ($this->getDebugShowResponseHeaders()) {
            $this->addCurlOptHeaderFunction($setOptArray, $headers);
        }

        if ($this->getIdempotencyKey()) {
            $headers[] = 'X-Header-Idempotency-Key: ' . $this->getIdempotencyKey();
        }

        curl_setopt_array($curl, $setOptArray);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if (true === $this->getDebugMode()) {
            $this->echoDebugMessage('POST-Запрос к серверу Ypmn:');
            $this->echoDebugMessage($encodedJsonData);
            $this->echoDebugMessage('Ответ от сервера Ypmn:');
            $this->echoDebugMessage(json_encode(json_decode($response), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

            if ($this->getDebugShowResponseHeaders()) {
                $this->echoDebugMessage('Заголовки ответа от сервера Ypmn:');
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
                $cpanel_url = 'https://' . ($this->getSandboxMode() ? 'sandbox' : 'secure' ). '.ypmn.ru/cpanel/';

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
            throw new PaymentException('Вы можете попробовать другой способ оплаты, либо свяжитесь с продавцом.');
        }

        return ['response' => $response, 'error' => $err];
    }

    /** @inheritdoc
     * @throws PaymentException
     */
    public function sendSessionRequest(SessionRequest $sessionRequest): array
    {
        return $this->sendPostRequest($sessionRequest, self::SESSION_API);
    }

    /** @inheritdoc */
    public function sendAuthRequest(PaymentInterface $payment): array
    {
        $paymentMethod = $payment->getAuthorization()->getPaymentMethod();
        $methodsWithPage = [
            PaymentMethods::CCVISAMC,
            null,
        ];

        if (
            !in_array($paymentMethod, $methodsWithPage) // если метод не подразумевает страницу
            || (!empty($payment->getAuthorization()->getCardDetails())
                && !empty($payment->getAuthorization()->getCardDetails()->getNumber())
            ) // или передаются данные PCI-DSS
            || !empty($payment->getAuthorization()->getMerchantToken()) // или содержится токен мерчанта
            || (!empty($payment->getAuthorization()->getOneTimeUseToken())
                && !empty($payment->getAuthorization()->getOneTimeUseToken()->getToken())
            )  // или содержится одноразовый токен от "Secure Fields"
        ) {
            $payment->getAuthorization()->setUsePaymentPage(false);
        } elseif (empty($paymentMethod)) {
            $payment->getAuthorization()->setUsePaymentPage(true); // если метод не выбран
        }

        return $this->sendPostRequest($payment, self::AUTHORIZE_API);
    }

    /** @inheritdoc  */
    public function sendCaptureRequest(CaptureInterface $capture): array
    {
        return $this->sendPostRequest($capture, self::CAPTURE_API);
    }

    /** @inheritdoc  */
    public function sendRefundRequest(RefundInterface $refund): array
    {
        return $this->sendPostRequest($refund, self::REFUND_API);
    }

    /** @inheritdoc  */
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

    /** @inheritdoc  */
    public function sendTokenCreationRequest(PaymentReference $payuPaymentReference): array
    {
        return $this->sendPostRequest($payuPaymentReference, self::TOKEN_API);
    }

    /** @inheritdoc  */
    public function sendTokenPaymentRequest(MerchantToken $tokenHash): array
    {
        return $this->sendPostRequest($tokenHash, self::AUTHORIZE_API);
    }

    /** @inheritDoc */
    public function sendPayoutCreateRequest(PayoutInterface $payout)
    {
        return $this->sendPostRequest($payout, self::PAYOUT_CREATE_API);
    }

    /** @inheritdoc  */
    public function sendReportChartRequest(array $params): array
    {
        $this->setJsonDebugResponse(false);
        return $this->sendGetRequest(self::REPORT_CHART_API . '/?' . http_build_query($params));
    }

    /** @inheritdoc  */
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

    /** @inheritdoc  */
    public function sendReportGeneralRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_GENERAL_API . '/?' . http_build_query($params));
    }

    /** @inheritdoc  */
    public function sendReportOrderRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_ORDERS_API_V4 . '/?' . http_build_query($params));
    }

    /** @inheritdoc  */
    public function sendReportOrderDetailsRequest(array $params): array
    {
        return $this->sendGetRequest(self::REPORT_ORDER_DETAILS_API . '/?' . http_build_query($params));
    }

    /**
     * Подпись запроса
     * @param MerchantInterface $merchant Мерчант
     * @param string $date Дата
     * @param string $url адрес отправки запроса
     * @param string $httpMethod HTTP
     * @param string $bodyHash md5-хэш запроса
     * @return string подпись
     */
    private function getSignature(MerchantInterface $merchant, string $date, string $url, string $httpMethod, string $bodyHash): string
    {
        if (strlen($merchant->getCode()) < 2) {
            throw new PaymentException('YPMN-001: не установлен код мерчанта, уточните его в личном кабинете или у Вашего менеджера');
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
        }
        $this->sandboxModeIsOn = $sandboxModeIsOn;
        $this->host = self::SANDBOX_HOST;

        return $this;
    }

    /** @inheritdoc  */
    public function getLocalMode(): bool
    {
        return $this->localModeIsOn;
    }

    /** @inheritdoc  */
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
    public function  setJsonDebugResponse(bool $jsonDebugResponse): self
    {
        $this->jsonDebugResponse = $jsonDebugResponse;
        return $this;
    }

    /** @inheritdoc  */
    public function  getJsonDebugResponse(): bool
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
                        padding: 2px;
                        border: 1px solid green;
                        white-space: pre-wrap;
                    "
                ><code>'.print_r($mixedInput, true).'</code></pre>';
        }
    }

    /** @inheritdoc  */
    public function sendPodeliRegistrationMerchantRequest(PodeliMerchant $merchant): array
    {
        return $this->sendPostRequest($merchant, self::PODELI_MERCHANT_REGISTRATION_API);
    }

    /** @inheritdoc  */
    public function sendQstCreateRequest(QstInterface $qst): array
    {
        return $this->sendPostRequest($qst, self::QST_CREATE_API);
    }

    /** @inheritdoc  */
    public function sendQstStatusRequest(int $qstId): array
    {
        return $this->sendGetRequest(self::QST_STATUS_API . '/' . $qstId);
    }

    /** @inheritdoc  */
    public function sendQstPrintRequest(int $qstId): array
    {
        return $this->sendGetRequest(self::QST_PRINT_API . '/' . $qstId);
    }

    /** @inheritdoc  */
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

    /**
     * @param array $curlOptArr
     * @param array $headers
     * @return void
     */
    private function addCurlOptHeaderFunction(array &$curlOptArr, array &$headers): void
    {
        $curlOptArr += [
            CURLOPT_HEADERFUNCTION => static function($curl, $header) use (&$headers)
            {
                if (strlen(trim($header)) > 0) {
                    $headers[] = trim($header);
                }

                return strlen($header);
            }
        ];
    }
}
