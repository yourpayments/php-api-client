<?php declare(strict_types=1);

namespace Ypmn\Payout;

class PayoutCommission implements PayoutCommissionInterface
{
    /** @var float Комиссия мерчанта */
    private float $merchant;

    /** @var float Комиссия бенефициара */
    private float $payee;

    /**
     * @inheritDoc
     */
    public function getMerchant(): float
    {
        return $this->merchant;
    }

    /**
     * @inheritDoc
     */
    public function setMerchant(float $merchant): self
    {
        $this->merchant = $merchant;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPayee(): float
    {
        return $this->payee;
    }

    /**
     * @inheritDoc
     */
    public function setPayee(float $payee): self
    {
        $this->payee = $payee;
        return $this;
    }
}