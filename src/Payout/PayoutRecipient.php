<?php declare(strict_types=1);

namespace Ypmn\Payout;

class PayoutRecipient implements PayoutRecipientInterface
{
    /** @var string Город получателя */
    private string $city;

    /** @var string Адрес получателя */
    private string $address;

    /** @var string Индекс */
    private string $postalCode;

    /** @var string Код страны */
    private string $countryCode;

    /** @var string Имя получателя */
    private string $clientName;

    /**
     * @inheritDoc
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @inheritDoc
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @inheritDoc
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @inheritDoc
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @inheritDoc
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @inheritDoc
     */
    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;
        return $this;
    }
}