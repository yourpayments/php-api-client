<?php

declare(strict_types=1);

namespace Ypmn;

interface WebhookStoredCredentialsInterface
{
    /**
     * Установить токен подписки SberPay
     * @return $this
     * @throws PaymentException Ошибка оплаты
     */
    public function setYpmnBindingId(string $ypmnBindingId): self;

    /**
     * Получить токен подписки SberPay
     * @return string|null
     */
    public function getYpmnBindingId(): ?string;
}
