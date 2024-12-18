<?php declare(strict_types=1);

namespace Ypmn;

use JsonSerializable;

class Capture implements CaptureInterface, JsonSerializable, TransactionInterface, PaymentDetailsInterface
{
    /** @var string Номер платежа Ypmn */
    private string $payuPaymentReference;

    /** @var float Cумма исходной операции на авторизацию */
    private float $originalAmount;

    /** @var float Сумма списания */
    private float $amount;

    /** @var string Валюта */
    private string $currency;

    /** @var Details|null Данные с расширенными сведениями в парах ключ/значение */
    private ?Details $details = null;

    /** @inheritDoc */
    public function setYpmnPaymentReference(string $paymentIdString): CaptureInterface
    {
        $this->payuPaymentReference = $paymentIdString;

        return $this;
    }

    /** @inheritDoc */
    public function getYpmnPaymentReference(): string
    {
        return $this->payuPaymentReference;
    }

    /** @inheritDoc */
    public function setOriginalAmount(float $originalAmount): CaptureInterface
    {
        $this->originalAmount = $originalAmount;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalAmount(): float
    {
        return $this->originalAmount;
    }

    /**
     * @inheritDoc
     * @throws PaymentException
     */
    public function setAmount(float $amount): CaptureInterface
    {
        if ($amount > $this->originalAmount) {
            throw new PaymentException('Списываемая сумма не должна быть больше суммы авторизации');
        }
        $this->amount = $amount;

        return $this;
    }

    /** @inheritDoc */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /** @inheritDoc */
    public function setCurrency(string $currency): CaptureInterface
    {
        // TODO: Implement Currency check method (in Currency Class).
        // TODO: Create Class Currency, pass Currency object to the constructors... (Payment, Capture and Refund Classes)
        $this->currency = $currency;

        return $this;
    }

    /** @inheritDoc */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @inheritDoc */
    public function setDebugMode(bool $isOn): CaptureInterface
    {
        // TODO: Implement setDebugMode() method.
        return $this;
    }

    /** @inheritDoc */
    public function setSandboxMode(bool $isOn): CaptureInterface
    {
        // TODO: Implement setSandboxMode() method.
        return $this;
    }

    /** @inheritDoc */
    public function getDetails(): ?Details
    {
        return $this->details;
    }

    /** @inheritDoc */
    public function setDetails(?Details $details): self
    {
        $this->details = $details;
        return $this;
    }

    #[\ReturnTypeWillChange]
    /** @inheritDoc */
    public function jsonSerialize()
    {
        //TODO: проверка необходимых параметров
        $requestData = [
            'payuPaymentReference'	=> $this->getYpmnPaymentReference(),
            'originalAmount'	=> $this->getOriginalAmount(),
            'amount'	=> $this->getAmount(),
            'currency' => $this->getCurrency()
        ];

        if ($this->details) {
            $requestData['details'] = $this->details->toArray();
        }

        return json_encode($requestData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
