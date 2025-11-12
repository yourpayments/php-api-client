<?php

declare(strict_types=1);

namespace Ypmn;

interface AmountInterface
{

    /**
     * @return float
     */
    public function getValue(): float;


    /**
     * @param float $value
     * @return Amount
     * @throws PaymentException
     */
    public function setValue(float $value): self;

    /**
     * @return string
     */
    public function getCurrency(): string;

    /**
     * @param string $currency
     * @return Amount
     */
    public function setCurrency(string $currency): self;






}
