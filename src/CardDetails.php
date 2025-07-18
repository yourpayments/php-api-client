<?php declare(strict_types=1);

namespace Ypmn;

class CardDetails implements CardDetailsInterface
{
    /** @var string Номер карты */
    private string $number;

    /** @var int Месяц прекращения действия Карты */
    private int $expiryMonth;

    /** @var int Год прекращения действия Карты */
    private int $expiryYear;

    /** @var string CVV Карты */
    private string $cvv;

    /** @var string Имя Владельца Карты */
    private string $owner;

    /** @var int Время набора Номера Карты (сек) */
    private int $timeSpentTypingNumber;

    /** @var int Время набора Имени Владельца Карты (сек) */
    private int $timeSpentTypingOwner;

    /** @var int BIN (Bank Identification Number) */
    private int $bin;

    /** @var string PAN (Permanent Account Number) */
    private string $pan;

    /** @var string Тип Карты (например, Visa, MIR) */
    private string $type;

    /** @var string Банк, выпустивший карту */
    private string $cardIssuerBank;

    /** @inheritDoc */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /** @inheritDoc */
    public function setNumber(string $number): self
    {
        //TODO: проверка на валидность
        $this->number = $number;

        return $this;
    }

    /** @inheritDoc */
    public function getExpiryMonth(): ?int
    {
        return $this->expiryMonth ?? null;
    }

    /** @inheritDoc */
    public function setExpiryMonth(int $expiryMonth): self
    {
        $this->expiryMonth = $expiryMonth;
        return $this;
    }

    /** @inheritDoc */
    public function getYear(): ?int
    {
        return $this->year ?? null;
    }

    /** @inheritDoc */
    public function setYear(int $year): self
    {
        if ( $year< 1900) {
            throw new PaymentException('Проверьте год выпуска карты');
        }

        $this->year = $year;
        return $this;
    }

    /** @inheritDoc */
    public function getExpiryYear(): ?int
    {
        return $this->expiryYear ?? null;
    }

    /** @inheritDoc */
    public function setExpiryYear(int $expiryYear): self
    {
        $this->expiryYear = $expiryYear;
        return $this;
    }

    /** @inheritDoc */
    public function getCvv(): ?string
    {
        return $this->cvv ?? null;
    }

    /** @inheritDoc */
    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }

    /** @inheritDoc */
    public function getOwner(): ?string
    {
        return $this->owner ?? null;
    }

    /** @inheritDoc */
    public function setOwner(string $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /** @inheritDoc */
    public function getTimeSpentTypingNumber(): ?int
    {
        return $this->timeSpentTypingNumber ?? null;
    }

    /** @inheritDoc */
    public function setTimeSpentTypingNumber(int $timeSpentTypingNumber): self
    {
        $this->timeSpentTypingNumber = $timeSpentTypingNumber;
        return $this;
    }

    /** @inheritDoc */
    public function getTimeSpentTypingOwner(): ?int
    {
        return $this->timeSpentTypingOwner ?? null;
    }

    /** @inheritDoc */
    public function setTimeSpentTypingOwner(int $timeSpentTypingOwner): self
    {
        $this->timeSpentTypingOwner = $timeSpentTypingOwner;
        return $this;
    }

    /** @inheritDoc */
    public function getBin(): ?int
    {
        return $this->bin ?? null;
    }

    /** @inheritDoc */
    public function setBin(int $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    /** @inheritDoc */
    public function getPan(): ?string
    {
        return $this->pan ?? null;
    }

    /** @inheritDoc */
    public function setPan(string $pan): self
    {
        $this->pan = $pan;
        return $this;
    }

    /** @inheritDoc */
    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    /** @inheritDoc */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /** @inheritDoc */
    public function getCardIssuerBank(): ?string
    {
        return $this->cardIssuerBank ?? null;
    }

    /** @inheritDoc */
    public function setCardIssuerBank(string $cardIssuerBank): self
    {
        $this->cardIssuerBank = $cardIssuerBank;
        return $this;
    }

    /** @inheritDoc */
    public function toArray() : array
    {
        $resultArray = get_object_vars($this);

        foreach ($resultArray as &$value) {
            if (is_object($value) && method_exists($value, 'toArray')) {

                $value = $value->toArray();

            } else {
                if (is_array($value)) {
                    foreach ($value as &$arrayValue) {
                        if (is_object($arrayValue) && method_exists($arrayValue, 'toArray')) {

                            $arrayValue = $arrayValue->toArray();
                        }
                    }
                }
            }
        }

        return $resultArray;
    }
}
