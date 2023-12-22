<?php

namespace Ypmn;

interface DestinationInterface
{
    /**
     * Получить Тип направления платежа
     * @return string
     */
    public function getType(): string;

    /**
     * Установить Тип направления платежа
     * @param string $type
     * @return PayoutDestination
     * @throws PaymentException
     */
    public function setType(string $type): self;

    public function arraySerialize() : array;

    public function getDetails(): ?DetailsInterface;

    public function setDetails(?DetailsInterface $details): self;

    /**
     * Получить биллинговую информацию о Получателе платежа
     * @return Billing|null
     */
    public function getRecipient(): ?Billing;

    /**
     * Установить биллинговую информацию о Получателе платежа
     * @param Billing|null $recipient
     * @return PayoutDestination
     */
    public function setRecipient(?Billing $recipient): self;
}
