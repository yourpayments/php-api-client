<?php declare(strict_types=1);

namespace Ypmn;

class WebhookStoredCredentials implements WebhookStoredCredentialsInterface
{
    private string $ypmnBindingId;
    private string $useId;

    public function setYpmnBindingId(string $ypmnBindingId): self
    {
        $this->ypmnBindingId = $ypmnBindingId;
        return $this;
    }

    public function getYpmnBindingId(): ?string
    {
        return $this->ypmnBindingId ?? null;
    }

    public function setUseId(string $useId): self
    {
        $this->useId = $useId;
        return $this;
    }

    public function getUseId(): ?string
    {
        return $this->useId ?? null;
    }
}
