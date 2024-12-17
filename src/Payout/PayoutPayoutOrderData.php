<?php declare(strict_types=1);

namespace Ypmn\Payout;

class PayoutPayoutOrderData implements PayoutOrderDataInterface
{
    /** @var string Дата Заказа */
    private string $orderDate;

    /** @var string Номер платежа Ypmn */
    private string $payuPayoutReference;

    /** @var string Номер Заказа у Мерчанта */
    private string $merchantPayoutReference;

    /** @var string Состояние */
    private string $status;

    /** @var string Валюта */
    private string $currency;

    /** @var float Подитог */
    private float $amount;

    /** @var string Текстовое сообщение */
    private string $message;

    /**
     * @inheritDoc
     */
    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    /**
     * @inheritDoc
     */
    public function setOrderDate(string $orderDate): self
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPayuPayoutReference(): string
    {
        return $this->payuPayoutReference;
    }

    /**
     * @inheritDoc
     */
    public function setPayuPayoutReference(string $payuPayoutReference): self
    {
        $this->payuPayoutReference = $payuPayoutReference;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMerchantPayoutReference(): string
    {
        return $this->merchantPayoutReference;
    }

    /**
     * @inheritDoc
     */
    public function setMerchantPayoutReference(string $merchantPayoutReference): self
    {
        $this->merchantPayoutReference = $merchantPayoutReference;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @inheritDoc
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
