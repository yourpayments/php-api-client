<?php declare(strict_types=1);

namespace Ypmn\Payout;

interface PayoutOrderDataInterface
{
    /**
     * Получить Дату Заказа
     * @return string Дата Заказа
     */
    public function getOrderDate(): string;

    /**
     * Установть Дату Заказа
     * @param string $orderDate Дата Заказа
     * @return self
     */
    public function setOrderDate(string $orderDate): self;

    /**
     * Получить Номер Заказа в Ypmn
     * @return string Номер Заказа в Ypmn
     */
    public function getPayuPayoutReference(): string;

    /**
     * Установить Номер Заказа в Ypmn
     * @param string $payuPayoutReference Номер Заказа в Ypmn
     * @return self
     */
    public function setPayuPayoutReference(string $payuPayoutReference): self;

    /**
     * Получить Номер Заказа у Мерчанта
     * @return string Номер Заказа у Мерчанта
     */
    public function getMerchantPayoutReference(): string;

    /**
     * Установить Номер Заказа у Мерчанта
     * @param string $merchantPayoutReference
     * @return self
     */
    public function setMerchantPayoutReference(string $merchantPayoutReference): self;

    /**
     * Получить Состояние Платежа
     * @return string Состояние Платежа
     */
    public function getStatus(): string;

    /**
     * Установить Состояние Платежа
     * @param string $status Состояние Платежа
     * @return self
     */
    public function setStatus(string $status): self;

    /**
     * Получить Валюту Платежа
     * @return string Валюта Платежа
     */
    public function getCurrency(): string;

    /**
     * Установить Валюту Платежа
     * @param string $currency Валюта Платежа
     * @return self
     */
    public function setCurrency(string $currency): self;

    /**
     * Получить Подитог
     * @return float Подитог
     */
    public function getAmount(): float;

    /**
     * Установить Подитог
     * @param float $amount Подитог
     * @return self
     */
    public function setAmount(float $amount): self;

    /**
     * Получить текстовое сообщение с подробной информацией о результате обработки или с сообщением о какой-либо ошибке
     * @return string
     */
    public function getMessage(): string;

    /**
     * Установить текстовое сообщение
     * @param string|null $message
     * @return self
     */
    public function setMessage(string $message): self;
}
