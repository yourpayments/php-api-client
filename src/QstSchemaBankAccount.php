<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Банковские реквизиты в анкете
 */
class QstSchemaBankAccount implements QstSchemaBankAccountInterface
{
    private string $bankBIK;
    private string $bankCorAccount;
    private string $bankAccount;

    /** @inheritdoc */
    public function getBankBIK(): string
    {
        return $this->bankBIK;
    }

    /** @inheritdoc */
    public function setBankBIK(string $bankBIK): self
    {
        $this->bankBIK = $bankBIK;
        return $this;
    }

    /** @inheritdoc */
    public function getBankCorAccount(): string
    {
        return $this->bankCorAccount;
    }

    /** @inheritdoc */
    public function setBankCorAccount(string $bankCorAccount): self
    {
        $this->bankCorAccount = $bankCorAccount;
        return $this;
    }

    /** @inheritdoc */
    public function getBankAccount(): string
    {
        return $this->bankAccount;
    }

    /** @inheritdoc */
    public function setBankAccount(string $bankAccount): self
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): array
    {
        return [
            'bankBIK' => $this->getBankBIK(),
            'bankCorAccount' => $this->getBankAccount(),
            'bankAccount' => $this->getBankAccount()
        ];
    }
}