<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Это класс для описания источника платежа
 **/
class PayoutSource implements PayoutSourceInterface
{
    /** @var string Тип источника, обычно это баланс для выплат */
    private string $type = 'merchantBalance';
    private Billing $sender;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /** @inheritdoc */
    public function getType(): string
    {
        return $this->type;
    }

    /** @inheritdoc */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /** @inheritdoc */
    public function getSender(): Billing
    {
        return $this->sender;
    }

    /** @inheritdoc */
    public function setSender(Billing $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /** @inheritdoc */
    public function arraySerialize() : array
    {
        //TODO: проверка параметров перед отправкой

        return [
            'type' => $this->getType(),
            'sender' => [
                'firstName' => $this->getSender()->getFirstName(),
                'lastName' => $this->getSender()->getLastName(),
                'email' => $this->getSender()->getEmail(),
                'phone' => $this->getSender()->getPhone(),
            ],
        ];
    }
}
