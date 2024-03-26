<?php declare(strict_types=1);

namespace Ypmn;

class PodeliMerchantAddress
{
    /** @notBlank */
    private string $index;

    /** @notBlank */
    private string $city;

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): PodeliMerchantAddress
    {
        $this->index = $index;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): PodeliMerchantAddress
    {
        $this->city = $city;

        return $this;
    }

    public function getAsArray(): array
    {
        return array_filter([
                                'index' => $this->index,
                                'city' => $this->city,
                            ], fn($value) => !is_null($value));
    }
}
