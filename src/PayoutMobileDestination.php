<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Это класс для описания направления платежа по сбп
 **/
class PayoutMobileDestination implements DestinationInterface
{
    private const AVAILABLE_TYPES = [
        'sbp',
    ];

    private string $type;
    private ?DetailsInterface $details = null;
    private ?Billing $recipient = null;

    /**
     * @throws PaymentException
     */
    public function __construct(string $type = 'sbp')
    {
        $this->setType($type);
    }

    /** @inheritdoc */
    public function getType(): string
    {
        return $this->type;
    }

    /** @inheritdoc */
    public function setType(string $type): self
    {
        if (!in_array($type, self::AVAILABLE_TYPES)) {
            throw new PaymentException('Недопустимый тип выплаты');
        }

        $this->type = $type;

        return $this;
    }

    /** @inheritdoc */
    public function getDetails(): ?DetailsInterface
    {
        return $this->details;
    }

    /** @inheritdoc */
    public function setDetails(?DetailsInterface $details): self
    {
        $this->details = $details;

        return $this;
    }

    /** @inheritdoc */
    public function getRecipient(): ?Billing
    {
        return $this->recipient;
    }

    /** @inheritdoc */
    public function setRecipient(?Billing $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    /** @inheritdoc */
    public function setPhoneNumber(string $phoneNumber): self
    {
        if ($this->getDetails() === null) {
            $this->setDetails(new PhoneDetails());
        }

        $this->getDetails()->setNumber($phoneNumber);

        return $this;
    }

    /** @inheritdoc */
    public function setBankInformation(int $bankId, string $bankName): self
    {
        if ($this->getDetails() === null) {
            $this->setDetails(new PhoneDetails());
        }

        $this->getDetails()->setBankId($bankId);
        $this->getDetails()->setBankName($bankName);

        return $this;
    }

    /**
     * @return array
     */
    public function arraySerialize() : array
    {
        $address = $this->getRecipient()->getAddressLine1()
            . ( $this->getRecipient()->getAddressLine2() ? '' . $this->getRecipient()->getAddressLine2() : null);

        return [
            'type' => $this->getType(),
            'sbp' => [
                'phoneNumber' => $this->getDetails()->getNumber(),
                "bankId" => $this->getDetails()->getBankId(),
                "bankName" => $this->getDetails()->getBankName()
            ],
            'recipient' => [
                'type' => $this->getRecipient()->getType(),
                'email' => $this->getRecipient()->getEmail(),
                'city' => $this->getRecipient()->getCity(),
                'address' => $address,
                'postalCode' => $this->getRecipient()->getZipCode(),
                'countryCode' => $this->getRecipient()->getCountryCode(),
                'firstName' => $this->getRecipient()->getFirstName(),
                'lastName' => $this->getRecipient()->getLastName()
            ],
        ];
    }
}
