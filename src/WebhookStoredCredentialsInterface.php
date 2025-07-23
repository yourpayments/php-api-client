<?php declare(strict_types=1);

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

    /**
     * Установить идентификатор первоначальной операции
     * @param string $useId
     * @return self
     */
    public function setUseId(string $useId): self;

    /**
     * Получить идентификатор первоначальной операции
     * @return string|null
     */
    public function getUseId(): ?string;
}
