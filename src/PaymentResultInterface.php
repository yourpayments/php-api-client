<?php declare(strict_types=1);

namespace Ypmn;

interface PaymentResultInterface
{
    /**
     * Получить Метод Оплаты
     * @return string Метод Оплаты
     */
    public function getPaymentMethod(): string;

    /**
     * Установить Метод Оплаты
     * @param string $paymentMethod Метод Оплаты
     * @return PaymentResult
     */
    public function setPaymentMethod(string $paymentMethod): self;

    /**
     * Получить Дату Авторизации платежа
     * @return string Дата Авторизации платежа
     */
    public function getPaymentDate(): string;

    /**
     * Установить Дату Авторизации платежа
     * @param string $paymentDate Дата Авторизации платежа
     * @return PaymentResult
     */
    public function setPaymentDate(string $paymentDate): self;

    /**
     * Получить Дату Списания денежных средств
     * @return string Дата Списания денежных средств
     */
    public function getCaptureDate(): string;

    /**
     * Установить Дату Списания денежных средств
     * @param string $captureDate Дата Списания денежных средств
     * @return PaymentResult
     */
    public function setCaptureDate(string $captureDate): self;

    /**
     * @return string
     */
    public function getCardProgramName(): string;

    /**
     * @param string $cardProgramName
     * @return PaymentResult
     */
    public function setCardProgramName(string $cardProgramName): self;

    /**
     * Получить Код Авторизации
     * @return string Код Авторизации
     */
    public function getAuthCode(): string;

    /**
     * Установить Код Авторизации
     * @param string $authCode Код Авторизации
     * @return PaymentResult
     */
    public function setAuthCode(string $authCode): self;

    /**
     * Получить Идентификатор марчанта (Merchant ID)
     * @return string Идентификатор марчанта (Merchant ID)
     */
    public function getMerchantId(): string;

    /**
     * Установить Идентификатор марчанта (Merchant ID)
     * @param string $merchantId Идентификатор марчанта (Merchant ID)
     * @return PaymentResult
     */
    public function setMerchantId(string $merchantId): self;

    /**
     * @return int
     */
    public function getRrn(): int;

    /**
     * @param int $rrn
     * @return PaymentResult
     */
    public function setRrn(int $rrn): self;

    /**
     * @return string
     */
    public function getInstallmentsNumber(): string;

    /**
     * @param string $installmentsNumber
     * @return $this
     */
    public function setInstallmentsNumber(string $installmentsNumber): self;

    /**
     * Получить Информацию о Карте
     * @param CardDetailsInterface $cardDetails Информация о Карте
     * @return $this
     */
    public function setCardDetails(CardDetailsInterface $cardDetails): self;

    /**
     * Установить Информацию о Карте
     * @param $cardDetails
     * @return CardDetailsInterface
     */
    public function getCardDetails($cardDetails) : CardDetailsInterface;


    /**
     * Получить Краткую запись Названия Банка Плательщика
     * @return string Краткая запись Названия Банка Плательщика
     */
    public function getPaymentBankShortName(): string;

    /**
     * Установить Краткую запись Названия Банка Плательщика
     * @param string $paymentBankShortName Краткая запись Названия Банка Плательщика
     * @return $this
     */
    public function setPaymentBankShortName(string $paymentBankShortName): self;

    /**
     * Получить Тип Сервиса Процессинга
     * @return string Тип Сервиса Процессинга
     */
    public function getServiceProcessingType(): string;

    /**
     * Установить Тип Сервиса Процессинга
     * @param string $serviceProcessingType Тип Сервиса Процессинга
     * @return $this
     */
    public function setServiceProcessingType(string $serviceProcessingType): self;
}
