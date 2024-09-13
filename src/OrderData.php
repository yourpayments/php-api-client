<?php declare(strict_types=1);

namespace Ypmn;

class OrderData implements OrderDataInterface
{
    /** @var string Дата Заказа */
    private string $orderDate;

    /** @var string Номер платежа Ypmn */
    private string $ypmnPaymentReference;

    /** @var string */
    private string $merchantPaymentReference;

    /** @var string Состояние */
    private string $status;

    /** @var string Валюта */
    private string $currency;

    /** @var float Подитог */
    private float $amount;
    
    /** @var float Комиссия */
    private float $commission;

    /** @var string Идентификатор запроса на возмещение средств */
    private string $refundRequestId;

    /** @var string Количество баллов лояльности */
    private string $loyaltyPointsAmount;

    /** @var array Детализация баллов лояльности */
    private array $loyaltyPointsDetails;

    /** @inheritDoc */
    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    /** @inheritDoc */
    public function setOrderDate(string $orderDate): self
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    /** @inheritDoc */
    public function getUpmnPaymentReference(): string
    {
        return $this->ypmnPaymentReference;
    }

    /** @inheritDoc */
    public function setYpmnPaymentReference(string $ypmnPaymentReference): self
    {
        $this->ypmnPaymentReference = $ypmnPaymentReference;
        return $this;
    }

    /** @inheritDoc */
    public function getYpmnPaymentReference(): string
    {
        return $this->ypmnPaymentReference;
    }

    /** @inheritDoc */
    public function getMerchantPaymentReference(): string
    {
        return $this->merchantPaymentReference;
    }

    /** @inheritDoc */
    public function setMerchantPaymentReference(string $merchantPaymentReference): self
    {
        $this->merchantPaymentReference = $merchantPaymentReference;
        return $this;
    }

    /** @inheritDoc */
    public function getStatus(): string
    {
        return $this->status;
    }

    /** @inheritDoc */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /** @inheritDoc */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @inheritDoc */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /** @inheritDoc */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /** @inheritDoc */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /** @inheritDoc */
    public function getCommission(): ?float
    {
        return $this->commission;
    }

    /** @inheritDoc */
    public function setCommission(float $commission): self
    {
        if ($commission > 0) {
            $this->commission = $commission;
        }

        return $this;
    }

    /** @inheritDoc */
    public function getRefundRequestId(): ?string
    {
        return $this->refundRequestId;
    }

    /** @inheritDoc */
    public function setRefundRequestId(string $refundRequestId): self
    {
        $this->refundRequestId = $refundRequestId;
        return $this;
    }

    /** @inheritDoc */
    public function getLoyaltyPointsAmount(): ?string
    {
        return $this->loyaltyPointsAmount;
    }

    /** @inheritDoc */
    public function setLoyaltyPointsAmount(string $loyaltyPointsAmount): self
    {
        $this->loyaltyPointsAmount = $loyaltyPointsAmount;
        return $this;
    }

    /** @inheritDoc */
    public function getLoyaltyPointsDetails(): ?array
    {
        return $this->loyaltyPointsDetails;
    }

    /** @inheritDoc */
    public function setLoyaltyPointsDetails(array $loyaltyPointsDetails): self
    {
        if(count($loyaltyPointsDetails) > 0) {
            $this->loyaltyPointsDetails = $loyaltyPointsDetails;
        }
        return $this;
    }
}
