<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaAddressInterface extends QstToArrayInterface
{
    /**
     * @return string
     */
    public function getZip(): string;

    /**
     * @param string $zip
     * @return $this
     */
    public function setZip(string $zip): self;

    /**
     * @return string
     */
    public function getRegion(): string;

    /**
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): self;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city): self;

    /**
     * @return string
     */
    public function getStreet(): string;

    /**
     * @param string $street
     * @return $this
     */
    public function setStreet(string $street): self;

    /**
     * @return string
     */
    public function getHouse(): string;

    /**
     * @param string $house
     * @return $this
     */
    public function setHouse(string $house): self;

    /**
     * @return string|null
     */
    public function getFlat(): ?string;

    /**
     * @param string $flat
     * @return $this
     */
    public function setFlat(string $flat): self;
}
