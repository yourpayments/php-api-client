<?php declare(strict_types=1);

namespace Ypmn\Payout;

class PayoutPaymentResult implements PayoutPaymentResultInterface
{
    /** @var string Метод Оплаты */
    private string $paymentMethod;

    /** @var string Дата Авторизации выплаты */
    private string $paymentDate;

    /** @var string|null Код Авторизации */
    private ?string $authCode = null;

    /** @var string|null Номер транзакции в Банке */
    private ?string $rrn = null;

    private PayoutCommissionInterface $commission;

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return PayoutPaymentResult
     */
    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDate(): string
    {
        return $this->paymentDate;
    }

    /**
     * @param string $paymentDate
     * @return PayoutPaymentResult
     */
    public function setPaymentDate(string $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @inheritDoc
     */
    public function setAuthCode(?string $authCode): self
    {
        $this->authCode = $authCode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRrn(): ?string
    {
        return $this->rrn;
    }

    /**
     * @inheritDoc
     */
    public function setRrn(?string $rrn): self
    {
        $this->rrn = $rrn;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCommission(): PayoutCommissionInterface
    {
        return $this->commission;
    }

    /**
     * @inheritDoc
     */
    public function setCommission(PayoutCommissionInterface $commission): self
    {
        $this->commission = $commission;
        return $this;
    }
}
