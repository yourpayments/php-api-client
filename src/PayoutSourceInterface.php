<?php

declare(strict_types=1);

namespace Ypmn;

interface PayoutSourceInterface
{
    /**
     * Получить Тип источника платежа
     * @return string
     */
    public function getType(): string;

    /**
     * Установить Тип источника платежа
     * @param string $type
     * @return PayoutSource
     */
    public function setType(string $type): self;

    /**
     * Получить информацию об отправителе
     * @return Billing
     */
    public function getSender(): Billing;

    /**
     * Установить информацию об отправителе
     * @param Billing $sender
     * @return PayoutSource
     */
    public function setSender(Billing $sender): self;

    /**
     * @return array
     */
    public function arraySerialize() : array;
}
