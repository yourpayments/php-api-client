<?php

namespace Ypmn;

interface ApiRequestInterface
{
    /**
     * Создание запроса от лица Мерчанта
     * @param MerchantInterface $merchant Мерчант
     */
    function __construct(MerchantInterface $merchant);

    /**
     * Запрос ID сессии
     * @param SessionRequest $sessionRequest
     * @return array
     */
    function sendSessionRequest(SessionRequest $sessionRequest): array;

    /**
     * Отправить Запрос на Оплату
     * @param PaymentInterface $payment Оплата
     * @return array
     */
    public function sendAuthRequest(PaymentInterface $payment): array;

    /**
     * Отправить Запрос на Списание Средств
     * @param CaptureInterface $capture Списание Средств
     * @return array
     */
    public function sendCaptureRequest(CaptureInterface $capture): array;

    /**
     * Отправить Запрос на Возврат
     * @param RefundInterface $refund Возврат
     * @return array
     */
    public function sendRefundRequest(RefundInterface $refund): array;

    /**
     * Отправить Запрос о статусе платежа
     * @param string $merchantPaymentReference Номер транзакции на стороне мерчанта
     * @return array
     */
    public function sendStatusRequest(string $merchantPaymentReference): array;

    /**
     * Установить режим песочницы
     * Переключить режим тестирования Ypmn Sandbox
     * оплата будет перенаправлена на тестовый сервер
     * @param bool $sandboxModeIsOn Режим песочницы включен?
     * @return $this
     */
    public function setSandboxMode(bool $sandboxModeIsOn): self;

    /**
     * Получить, установлен ли режим песочницы
     * @return bool Режим песочницы включен?
     */
    public function getSandboxMode(): bool;

    /**
     * Установить режим отладки
     * (скрипт будет выводить отладочные сообщения)
     * @param bool $debugModeIsOn Режим отладки включен?
     * @return $this
     */
    public function setDebugMode(bool $debugModeIsOn): self;

    /**
     * Получить, установлен ли режим отладки
     * @return bool Режим отладки включен?
     */
    public function getDebugMode(): bool;

    /**
     * Установить тип позвращаемого значения в режиме отладки
     * @return $this
     */
    public function  setJsonDebugResponse(bool $jsonDebugResponse): self;

    /**
     * Получить тип позвращаемого значения в режиме отладки
     * @return bool
     */
    public function  getJsonDebugResponse(): bool;

    /**
     * Отправить Запрос на Токенизацию
     * @param PaymentReference $payuPaymentReference Оплата
     * @return array
     */
    public function sendTokenCreationRequest(PaymentReference $payuPaymentReference): array;

    /**
     * Отправить Запрос на Оплату токеном
     * @param PaymentReference $payuPaymentReference Оплата
     * @return array
     */
    public function sendTokenPaymentRequest(MerchantToken $tokenHash): array;

    /**
     * Отправить запрос для получения графика
     * @param array $params
     */
    public function sendReportChartRequest(array $params);

    /**
     * Отправить запрос для получения JSON для обновления графика
     * @param array $params
     */
    public function sendReportChartUpdateRequest(array $params);

    /**
     * Отправить запрос для получения JSON данных отчета
     * @param array $params
     */
    public function sendReportGeneralRequest(array $params);

    /**
     * Отправить запрос быстрого отчёта по заказам для сверки
     * @param array $params
     */
    public function sendReportOrderRequest(array $params);

    /**
     * Отправить запрос для получения детального отчета по заказу
     * @param array $params
     */
    public function sendReportOrderDetailsRequest(array $params);

    /** @return string Хост для отправки запросов */
    public function getHost() : string;

    /**
     * @param string $host Хост для отправки запросов
     * @return $this
     * @throws PaymentException
     */
    public function setHost(string $host) : self;

    /**
     * Отправить запрос на регистрацию мерчанта
     * @param PodeliMerchant $merchant
     * @return array
     */
    public function sendPodeliRegistrationMerchantRequest(PodeliMerchant $merchant): array;

    /**
     * Отправить запрос на создание и отправку анкеты (по добавлению продавцов) на проверку
     * @param Qst $qst
     * @return array
     */
    public function sendQstCreateRequest(QstInterface $qst): array;

    /**
     * Получить статус анкеты
     * @param int $qstId
     * @return array
     */
    public function sendQstStatusRequest(int $qstId): array;

    /**
     * Распечатать анкету
     * @param int $qstId
     * @return array
     */
    public function sendQstPrintRequest(int $qstId): array;

    /**
     * Получить список анкет
     * @return array
     */
    public function sendQstListRequest(): array;

    /**
     * Получить установлен ли режим показывать заголовки ответа в режиме отладки
     * @return bool
     */
    public function getDebugShowResponseHeaders(): bool;

    /**
     * Установить показывать заголовки ответа в режиме отладки
     * @param bool $debugShowResponseHeaders
     * @return self
     */
    public function setDebugShowResponseHeaders(bool $debugShowResponseHeaders = true): self;
}
