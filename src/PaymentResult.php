<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Результат Платежа
 */
class PaymentResult implements PaymentResultInterface
{
    /** @var string Метод Оплаты */
    private string $paymentMethod;

    /** @var string Дата Авторизации платежа */
    private string $paymentDate;

    /** @var string Дата Списания денежных средств */
    private string $captureDate;

    /** @var string */
    private string $cardProgramName;

    /** @var string */
    private string $installmentsNumber;

    /** @var string Код Авторизации */
    private string $authCode;

    /** @var string Идентификатор марчанта (Merchant ID) */
    private string $merchantId;

    /** @var int Номер транзакции в Банке */
    private int $rrn;

    /** @var string Краткое Наименование Банка */
    private string $paymentBankShortName;

    /** @var string Тип Процессинга */
    private string $serviceProcessingType;

    /** @var CardDetailsInterface Информация о Карте */
    private CardDetailsInterface $cardDetails;

    /** @inheritDoc */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod ?? null;
    }

    /** @inheritDoc */
    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /** @inheritDoc */
    public function getPaymentDate(): ?string
    {
        return $this->paymentDate ?? null;
    }

    /** @inheritDoc */
    public function setPaymentDate(string $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /** @inheritDoc */
    public function getCaptureDate(): ?string
    {
        return $this->captureDate ?? null;
    }

    /** @inheritDoc */
    public function setCaptureDate(string $captureDate): self
    {
        $this->captureDate = $captureDate;
        return $this;
    }

    /** @inheritDoc */
    public function getCardProgramName(): ?string
    {
        return $this->cardProgramName ?? null;
    }

    /** @inheritDoc */
    public function setCardProgramName(string $cardProgramName): self
    {
        $this->cardProgramName = $cardProgramName;
        return $this;
    }

    /** @inheritDoc */
    public function getAuthCode(): ?string
    {
        return $this->authCode ?? null;
    }

    /** @inheritDoc */
    public function setAuthCode(string $authCode): self
    {
        $this->authCode = $authCode;
        return $this;
    }

    /** @inheritDoc */
    public function getMerchantId(): ?string
    {
        return $this->merchantId ?? null;
    }

    /** @inheritDoc */
    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /** @inheritDoc */
    public function getRrn(): ?int
    {
        return $this->rrn ?? null;
    }

    /** @inheritDoc */
    public function setRrn(int $rrn): self
    {
        $this->rrn = $rrn;
        return $this;
    }

    /** @inheritDoc */
    public function getInstallmentsNumber(): ?string
    {
        return $this->installmentsNumber ?? null;
    }

    /** @inheritDoc */
    public function setInstallmentsNumber(string $installmentsNumber): self
    {
        $this->installmentsNumber = $installmentsNumber;
        return $this;
    }

    /** @inheritDoc */
    public function setCardDetails(CardDetailsInterface $cardDetails): self
    {
        $this->cardDetails = $cardDetails;
        return $this;
    }

    /** @inheritDoc */
    public function getCardDetails() : CardDetailsInterface
    {
        return $this->cardDetails ?? new CardDetails();
    }

    /** @inheritDoc */
    public function getPaymentBankShortName(): ?string
    {
        return $this->paymentBankShortName ?? null;
    }

    /** @inheritDoc */
    public function setPaymentBankShortName(string $paymentBankShortName): self
    {
        $this->paymentBankShortName = $paymentBankShortName;
        return $this;
    }

    /** @inheritDoc */
    public function getServiceProcessingType(): ?string
    {
        return $this->serviceProcessingType ?? null;
    }

    /** @inheritDoc */
    public function setServiceProcessingType(string $serviceProcessingType): self
    {
        $this->serviceProcessingType = $serviceProcessingType;
        return $this;
    }
}
