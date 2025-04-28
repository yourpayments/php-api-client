<?php declare(strict_types=1);

namespace Ypmn;

interface WebhookAuthorizationInterface
{
    /**
     * Установить объект с учетными данными
     * @param WebhookStoredCredentialsInterface $storedCredentials
     * @return self
     */
    public function setStoredCredentials(WebhookStoredCredentialsInterface $storedCredentials): self;

    /**
     * Получить объект с учетными данными
     * @return WebhookStoredCredentialsInterface
     */
    public function getStoredCredentials(): WebhookStoredCredentialsInterface;
}
