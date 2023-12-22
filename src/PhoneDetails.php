<?php

namespace Ypmn;

class PhoneDetails implements DetailsInterface
{
    private string $number;
    private int $bankId;
    private string $bankName;

    public  function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public  function getBankId(): int
    {
        return $this->bankId;
    }

    public function setBankId(int $bankId): self
    {
        $this->bankId = $bankId;

        return $this;
    }

    public  function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }
}
