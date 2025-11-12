<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

class PodeliMerchantBankDetails
{
    /** @notBlank */
    private string $bankName;

    /** @notBlank */
    private string $bankIdentifierCode;

    /** @notBlank */
    private string $correspondingAccount;

    /** @notBlank */
    private string $paymentAccount;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): PodeliMerchantBankDetails
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getBankIdentifierCode(): string
    {
        return $this->bankIdentifierCode;
    }

    public function setBankIdentifierCode(string $bankIdentifierCode): PodeliMerchantBankDetails
    {
        $this->bankIdentifierCode = $bankIdentifierCode;

        return $this;
    }

    public function getCorrespondingAccount(): string
    {
        return $this->correspondingAccount;
    }

    public function setCorrespondingAccount(string $correspondingAccount): PodeliMerchantBankDetails
    {
        $this->correspondingAccount = $correspondingAccount;

        return $this;
    }

    public function getPaymentAccount(): string
    {
        return $this->paymentAccount;
    }

    public function setPaymentAccount(string $paymentAccount): PodeliMerchantBankDetails
    {
        $this->paymentAccount = $paymentAccount;

        return $this;
    }

    public function getAsArray(): array
    {
        return array_filter([
                                'bankName' => $this->bankName,
                                'bankIdentifierCode' => $this->bankIdentifierCode,
                                'correspondingAccount' => $this->correspondingAccount,
                                'paymentAccount' => $this->paymentAccount,
                            ], fn($value) => !is_null($value));
    }
}
