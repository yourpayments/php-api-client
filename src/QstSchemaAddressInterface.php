<?php declare(strict_types=1);

namespace Ypmn;

interface QstSchemaAddressInterface extends QstToArrayInterface
{
    /**
     * Индекс
     * @return string
     */
    public function getZip(): string;

    /**
     * Индекс
     * @param string $zip
     * @return $this
     */
    public function setZip(string $zip): self;

    /**
     * Регион
     * @return string
     */
    public function getRegion(): string;

    /**
     * Регион
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): self;

    /**
     * Город
     * @return string
     */
    public function getCity(): string;

    /**
     * Город
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self;

    /**
     * Улица
     * @return string
     */
    public function getStreet(): string;

    /**
     * Улица
     * @param string $street
     * @return $this
     */
    public function setStreet(string $street): self;

    /**
     * Дом
     * @return string
     */
    public function getHouse(): string;

    /**
     * Дом
     * @param string $house
     * @return $this
     */
    public function setHouse(string $house): self;

    /**
     * Офис / квартира
     * @return string|null
     */
    public function getFlat(): ?string;

    /**
     * Офис / квартира
     * @param string $flat
     * @return $this
     */
    public function setFlat(string $flat): self;
}
