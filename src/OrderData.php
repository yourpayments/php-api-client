<?php declare(strict_types=1);

namespace Ypmn;

class OrderData implements OrderDataInterface
{
    /** @var string Дата Заказа */
    private string $orderDate;

    /** @var string Номер платежа Ypmn */
    private string $payUPaymentReference;

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

    /** @var int|null Количество баллов лояльности */
    private ?int $loyaltyPointsAmount = null;

    /** @var array Детализация баллов лояльности */
    private array $loyaltyPointsDetails;

    /** @inheritDoc */
    public function getOrderDate(): ?string
    {
        return $this->orderDate ?? null;
    }

    /** @inheritDoc */
    public function setOrderDate(string $orderDate): self
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /** @inheritDoc */
    public function setYpmnPaymentReference(string $ypmnPaymentReference): self
    {
        $this->payUPaymentReference = $ypmnPaymentReference;

        return $this;
    }

    /** @inheritDoc */
    public function getYpmnPaymentReference(): ?string
    {
        return $this->payUPaymentReference ?? null;
    }

    /** @inheritDoc */
    public function setPayUPaymentReference(string $payUPaymentReference): self
    {
        $this->payUPaymentReference = $payUPaymentReference;

        return $this;
    }

    /** @inheritDoc */
    public function getPayUPaymentReference(): ?string
    {
        return $this->payUPaymentReference ?? null;
    }

    /** @inheritDoc */
    public function getMerchantPaymentReference(): ?string
    {
        return $this->merchantPaymentReference ?? null;
    }

    /** @inheritDoc */
    public function setMerchantPaymentReference(string $merchantPaymentReference): self
    {
        $this->merchantPaymentReference = $merchantPaymentReference;

        return $this;
    }

    /** @inheritDoc */
    public function getStatus(): ?string
    {
        return $this->status ?? null;
    }

    /** @inheritDoc */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /** @inheritDoc */
    public function getCurrency(): ?string
    {
        return $this->currency ?? null;
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
        return $this->amount ?? null;
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
        return $this->commission ?? null;
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
        return $this->refundRequestId ?? null;
    }

    /** @inheritDoc */
    public function setRefundRequestId(string $refundRequestId): self
    {
        $this->refundRequestId = $refundRequestId;
        return $this;
    }

    /** @inheritDoc */
    public function getLoyaltyPointsAmount(): ?int
    {
        return $this->loyaltyPointsAmount ?? null;
    }

    /** @inheritDoc */
    public function setLoyaltyPointsAmount(int $loyaltyPointsAmount): self
    {
        $this->loyaltyPointsAmount = $loyaltyPointsAmount;

        return $this;
    }

    /** @inheritDoc */
    public function getLoyaltyPointsDetails(): ?array
    {
        return $this->loyaltyPointsDetails ?? null;
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
