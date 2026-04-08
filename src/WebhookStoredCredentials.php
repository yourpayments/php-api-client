<?php declare(strict_types=1);

namespace Ypmn;

class WebhookStoredCredentials implements WebhookStoredCredentialsInterface
{
    private string $ypmnBindingId;

    public function setYpmnBindingId(string $ypmnBindingId): self
    {
        $this->ypmnBindingId = $ypmnBindingId;
        return $this;
    }

    public function getYpmnBindingId(): ?string
    {
        return $this->ypmnBindingId ?? null;
    }
}
