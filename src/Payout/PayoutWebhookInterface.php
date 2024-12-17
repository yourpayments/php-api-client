<?php declare(strict_types=1);

namespace Ypmn\Payout;

use Ypmn\PaymentException;

interface PayoutWebhookInterface
{
    /**
     * Принять запрос от сервера Ypmn
     * @return $this
     * @throws PaymentException Ошибка оплаты
     */
    public function catchJsonRequest(): self;

    /**
     * Получить информацию о заказе
     * @return PayoutOrderDataInterface
     */
    public function getOrderData(): PayoutOrderDataInterface;

    /**
     * Установить информацию о заказе
     * @param PayoutOrderDataInterface $payoutOrderData Информация о заказе
     * @return mixed
     */
    public function setOrderData(PayoutOrderDataInterface $payoutOrderData): self;

    /**
     * Получить результат выплаты
     * @return PayoutPaymentResultInterface
     */
    public function getPaymentResults(): PayoutPaymentResultInterface;

    /**
     * Установить результат выплаты
     * @param PayoutPaymentResultInterface $payoutPaymentResult Результат выплаты
     * @return self
     */
    public function setPaymentResults(PayoutPaymentResultInterface $payoutPaymentResult): self;

    /**
     * Получить информацию о получателе
     * @return PayoutRecipientInterface
     */
    public function getRecipient(): PayoutRecipientInterface;

    /**
     * Установить информацию о получателе
     * @param PayoutRecipientInterface $payoutRecipient Информация о получателе
     * @return self
     */
    public function setRecipient(PayoutRecipientInterface $payoutRecipient): self;
}