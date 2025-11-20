<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Это класс для описания направления платежа
 **/
class PayoutDestination implements PayoutDestinationInterface
{
    private const AVAILABLE_TYPES = [
        'card',
        'token',
    ];

    private string $type;
    private ?CardDetails $card = null;
    private ?Billing $recipient = null;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /**
     * @param string|null $type
     * @throws PaymentException
     */
    public function __construct(string $type = 'card')
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
    public function getCard(): ?CardDetails
    {
        return $this->card;
    }

    /** @inheritdoc */
    public function setCard(?CardDetails $card): self
    {
        $this->card = $card;

        return $this;
    }

    /** @inheritdoc */
    public function getDetails(): ?DetailsInterface
    {
        return $this->card;
    }

    /** @inheritdoc */
    public function setDetails(?DetailsInterface $details): self
    {
        $this->card = $details;

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
    public function setCardNumber(string $cardNumber): self
    {
        if ($this->getCard() === null) {
            $this->setCard(new CardDetails());
        }

        $this->getCard()->setNumber($cardNumber);

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
            'card' => [
                'cardNumber' => $this->getCard()->getNumber(),
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
