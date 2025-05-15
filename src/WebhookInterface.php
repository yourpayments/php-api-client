<?php declare(strict_types=1);

namespace Ypmn;

interface WebhookInterface
{
    /**
     * Принять запрос от сервера Ypmn
     * @return $this
     * @throws PaymentException Ошибка оплаты
     */
    public function catchJsonRequest(): self;

    /**
     * Получить Результат Платежа
     * @return PaymentResultInterface Результат Платежа
     */
    public function getPaymentResult(): PaymentResultInterface;

    /**
     * Установить Результат Платежа
     * @param PaymentResultInterface $paymentResult Результат Платежа
     * @return Webhook
     */
    public function setPaymentResult(PaymentResultInterface $paymentResult): self;

    /**
     * Получить Информацию о Заказе
     * @return OrderDataInterface Информация о Заказе
     */
    public function getOrderData(): OrderDataInterface;

    /**
     * Установить Информацию о Заказе
     * @param OrderDataInterface $orderData Информация о Заказе
     * @return Webhook
     */
    public function setOrderData(OrderDataInterface $orderData): self;

    /**
     * Установить информацию об авторизации
     * @param WebhookAuthorizationInterface $authorization
     * @return $this
     */
    public function setAuthorization(WebhookAuthorizationInterface $authorization): self;

    /**
     * Получить информацию об авторизации
     * @return WebhookAuthorizationInterface
     */
    public function getAuthorization(): WebhookAuthorizationInterface;
}
