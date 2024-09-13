<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Адрес продавца в анкете
 **/
abstract class QstSchemaAddressAbstract implements QstSchemaAddressInterface
{
    private string $zip;
    private string $region;
    private string $city;
    private string $street;
    private string $house;
    private ?string $flat = null;

    /** @inheritdoc */
    public function getZip(): string
    {
        return $this->zip;
    }

    /** @inheritdoc */
    public function setZip(string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }

    /** @inheritdoc */
    public function getRegion(): string
    {
        return $this->region;
    }

    /** @inheritdoc */
    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    /** @inheritdoc */
    public function getCity(): string
    {
        return $this->city;
    }

    /** @inheritdoc */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /** @inheritdoc */
    public function getStreet(): string
    {
        return $this->street;
    }

    /** @inheritdoc */
    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    /** @inheritdoc */
    public function getHouse(): string
    {
        return $this->house;
    }

    /** @inheritdoc */
    public function setHouse(string $house): self
    {
        $this->house = $house;
        return $this;
    }

    /** @inheritdoc */
    public function getFlat(): ?string
    {
        return $this->flat;
    }

    /** @inheritdoc */
    public function setFlat(string $flat): self
    {
        $this->flat = $flat;
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): ?array
    {
        $array = [
            'zip' => $this->getZip(),
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'street' => $this->getStreet(),
            'house' => $this->getHouse(),
            'flat' => $this->getFlat()
        ];

        return array_filter($array, static fn ($value) => $value !== null);
    }
}
