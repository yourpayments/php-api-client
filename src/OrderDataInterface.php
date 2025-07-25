<?php declare(strict_types=1);

namespace Ypmn;

interface OrderDataInterface
{

    /**
     * Получить Дату Заказа
     * @return string|null Дата Заказа
     */
    public function getOrderDate(): ?string;

    /**
     * Установть Дату Заказа
     * @param string $orderDate
     * @return $this Дата Заказа
     */
    public function setOrderDate(string $orderDate): self;

    /**
     * Получить Номер Заказа в Ypmn
     * @return string|null Номер Заказа в Ypmn
     *
     * @deprecated Используйте getPayUPaymentReference
     */
    public function getYpmnPaymentReference(): ?string;

    /**
     * Установить Номер Заказа в Ypmn
     * @param string $ypmnPaymentReference Номер Заказа в Ypmn
     * @return $this
     *
     * @deprecated Используйте setPayUPaymentReference
     */
    public function setYpmnPaymentReference(string $ypmnPaymentReference): self;

    /**
     * Получить Номер Заказа в Ypmn
     * @return string|null Номер Заказа в Ypmn
     */
    public function getPayUPaymentReference(): ?string;
    /**
     * Установить Номер Заказа в Ypmn
     * @param string $payUPaymentReference Номер Заказа в Ypmn
     * @return $this
     */
    public function setPayUPaymentReference(string $payUPaymentReference): self;

    /**
     * Получить Номер Заказа у Мерчанта
     * @return string|null Номер Заказа у Мерчанта
     */
    public function getMerchantPaymentReference(): ?string;

    /**
     * Установить Номер Заказа у Мерчанта
     * @param string $merchantPaymentReference
     * @return $this
     */
    public function setMerchantPaymentReference(string $merchantPaymentReference): self;

    /**
     * Получить Состояние Платежа
     * @return string|null Состояние Платежа
     */
    public function getStatus(): ?string;

    /**
     * Установить Состояние Платежа
     * @param string $status Состояние Платежа
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * Получить Валюту Платежа
     * @return string|null Валюта Платежа
     */
    public function getCurrency(): ?string;

    /**
     * Установить Валюту Платежа
     * @param string $currency Валюта Платежа
     * @return $this
     */
    public function setCurrency(string $currency): self;

    /**
     * Получить Подитог
     * @return float Подитог
     */
    public function getAmount(): ?float;

    /**
     * Установить Подитог
     * @param float $amount Подитог
     * @return $this
     */
    public function setAmount(float $amount): self;

    /**
     * Получить Комиссию
     * @return float Комиссия
     */
    public function getCommission(): ?float;

    /**
     * Установить Комиссию
     * @param float $commission Комиссия
     * @return $this
     */
    public function setCommission(float $commission): self;

    /**
     * Получить ID запроса возврата
     * @return string|null
     */
    public function getRefundRequestId(): ?string;

    /**
     * Установить ID запроса возврата
     * @param string $refundRequestId
     * @return $this
     */
    public function setRefundRequestId(string $refundRequestId): self;

    /**
     * Получить Количество Баллов Лояльности
     * @return int|null Количество Баллов Лояльности
     */
    public function getLoyaltyPointsAmount(): ?int;

    /**
     * Установить Количество Баллов Лояльности
     * @param int $loyaltyPointsAmount
     * @return $this Количество Баллов Лояльности
     */
    public function setLoyaltyPointsAmount(int $loyaltyPointsAmount): self;

    /**
     * Получить Детализацию Баллов Лояльности
     * @return array|null Детализация Баллов Лояльности
     */
    public function getLoyaltyPointsDetails(): ?array;

    /**
     * Установить Детализацию Баллов Лояльности
     * @param array $loyaltyPointsDetails Детализация Баллов Лояльности
     * @return $this
     */
    public function setLoyaltyPointsDetails(array $loyaltyPointsDetails): self;
}
