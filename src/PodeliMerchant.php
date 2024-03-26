<?php declare(strict_types=1);

namespace Ypmn;

use JsonSerializable;

class PodeliMerchant implements JsonSerializable
{
    /**
     * @notBlank
     *
     */
    private string $login;

    /** @notBlank */
    private string $name;

    /** @notBlank */
    private string $legalEntity;

    /** @notBlank */
    private string $inn;

    /** @notBlank */
    private string $mcc;

    /** @notBlank */
    private string $email;

    /** @notBlank */
    private string $siteUrl;

    /** @notBlank */
    private string $agreementSignDate;

    /**
     * @isLinkType
     */
    private PodeliMerchantBankDetails $requisite;

    /**
     * @isLinkType
     */
    private PodeliMerchantAddress $address;

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): PodeliMerchant
    {
        $this->login = $login;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PodeliMerchant
    {
        $this->name = $name;

        return $this;
    }

    public function getLegalEntity(): string
    {
        return $this->legalEntity;
    }

    public function setLegalEntity(string $legalEntity): PodeliMerchant
    {
        $this->legalEntity = $legalEntity;

        return $this;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function setInn(string $inn): PodeliMerchant
    {
        $this->inn = $inn;

        return $this;
    }

    public function getMcc(): string
    {
        return $this->mcc;
    }

    public function setMcc(string $mcc): PodeliMerchant
    {
        $this->mcc = $mcc;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): PodeliMerchant
    {
        $this->email = $email;

        return $this;
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }

    public function setSiteUrl(string $siteUrl): PodeliMerchant
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getAgreementSignDate(): string
    {
        return $this->agreementSignDate;
    }

    public function setAgreementSignDate(string $agreementSignDate): PodeliMerchant
    {
        $this->agreementSignDate = $agreementSignDate;

        return $this;
    }

    public function getBankDetails(): PodeliMerchantBankDetails
    {
        return $this->requisite;
    }

    public function setBankDetails(PodeliMerchantBankDetails $requisite): PodeliMerchant
    {
        $this->requisite = $requisite;

        return $this;
    }

    public function getAddress(): PodeliMerchantAddress
    {
        return $this->address;
    }

    public function setAddress(PodeliMerchantAddress $address): PodeliMerchant
    {
        $this->address = $address;

        return $this;
    }

    public function getAsArray(): array
    {
        return array_filter([
                                'login' => $this->login,
                                'name' => $this->name,
                                'legalEntity' => $this->legalEntity,
                                'inn' => $this->inn,
                                'mcc' => $this->mcc,
                                'email' => $this->email,
                                'siteUrl' => $this->siteUrl,
                                'agreementSignDate' => $this->agreementSignDate,
                                'requisite' => $this->requisite->getAsArray(),
                                'address' => $this->address->getAsArray(),
                            ], fn($value) => !is_null($value));
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return json_encode($this->getAsArray(), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
