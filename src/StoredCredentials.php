<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

class StoredCredentials implements StoredCredentialsInterface
{
    /** @var string  */
    private string $useType;

    /** @var string id исходной операции */
    private string $useId;

    /**
     * Тип использования привязки (OnDemand или recursive).
     *
     * @var string $consentType
     */
    private string $consentType;

    /**
     * Причина создания привязки.
     *
     * @var string $subscriptionPurpose
     */
    private string $subscriptionPurpose;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /** @param string|null $useType */
    public function __construct(?string $useType = null)
    {
        if (!empty($useType)) {
            $this->setUseType($useType);
        }
    }

    /** @inheritDoc */
    public function getUseType(): string
    {
        return $this->useType;
    }

    /** @inheritDoc */
    public function setUseType(string $useType): self
    {
        $this->useType = $useType;
        return $this;
    }

    /** @inheritDoc */
    public function getUseId(): string
    {
        return $this->useId;
    }

    /** @inheritDoc */
    public function setUseId(string $useId): self
    {
        $this->useId = $useId;
        return $this;
    }

    /** @inheritDoc */
    public function getConsentType(): string
    {
        return $this->consentType;
    }

    /** @inheritDoc */
    public function setConsentType(string $consentType): self
    {
        $this->consentType = $consentType;
        return $this;
    }

    /** @inheritDoc */
    public function getSubscriptionPurpose(): string
    {
        return $this->subscriptionPurpose ?? "For future use";
    }

    /** @inheritDoc */
    public function setSubscriptionPurpose(string $subscriptionPurpose): self
    {
        $this->subscriptionPurpose = $subscriptionPurpose;
        return $this;
    }

    /** @inheritDoc */
    public function arraySerialize(): array
    {
        $returnArray = [];

        if (empty($this->useType) === false) {
            $returnArray["useType"] = $this->useType;
        }

        if (empty($this->useId) === false) {
            $returnArray["useId"] = $this->useId;
        }

        if (empty($this->consentType) === false) {
            $returnArray["consentType"] = $this->consentType;
        }

        if (empty($this->subscriptionPurpose) === false) {
            $returnArray["subscriptionPurpose"] = $this->subscriptionPurpose;
        }

        return $returnArray;
    }
}
