<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Количество денежных средств (сколько + валюта)
 **/
class Amount implements AmountInterface
{
    /**
     * Value
     * @var float
     */
    private float $value;

    /**
     * The ISO 4217 currency code (e.g., 'RUB', 'USD', 'EUR')
     * @var string
     */
    private string $currency = 'RUB';

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /** @throws PaymentException */
    public function __construct(float $value, $currency='RUB')
    {
        $this->setValue($value);

        $this->setCurrency($currency);
    }

    /** @inheritdoc */
    public function getValue(): float
    {
        return $this->value;
    }

    /** @inheritdoc
     */
    public function setValue(float $value): self
    {
        if ($value < 1) {
            throw new PaymentException('Слишком маленькая выплата');
        }

        $this->value = $value;

        return $this;
    }

    /** @inheritdoc */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @inheritdoc */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /** @inheritdoc */
    public function arraySerialize() : array
    {
        return [
            "currency" => $this->getCurrency() ?? 'RUB',
            "value" => round( $this->getValue(), 2),
        ];
    }
}
