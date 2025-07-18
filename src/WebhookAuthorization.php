<?php declare(strict_types=1);

namespace Ypmn;

class WebhookAuthorization implements WebhookAuthorizationInterface
{
    private WebhookStoredCredentialsInterface $storedCredentials;

    /** @inheritDoc */
    public function setStoredCredentials(WebhookStoredCredentialsInterface $storedCredentials): self
    {
        $this->storedCredentials = $storedCredentials;
        return $this;
    }

    /** @inheritDoc */
    public function getStoredCredentials(): WebhookStoredCredentialsInterface
    {
        return $this->storedCredentials ?? new WebhookStoredCredentials();
    }
}
