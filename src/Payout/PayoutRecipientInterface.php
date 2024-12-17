<?php declare(strict_types=1);

namespace Ypmn\Payout;

interface PayoutRecipientInterface
{
    /**
     * Получить город получателя
     * @return string
     */
    public function getCity(): string;

    /**
     * Установить город получателя
     * @param string $city Город получателя
     * @return self
     */
    public function setCity(string $city): self;

    /**
     * Получить адрес получателя
     * @return string
     */
    public function getAddress(): string;

    /**
     * Установить адрес получателя
     * @param string $address адрес получателя
     * @return self
     */
    public function setAddress(string $address): self;

    /**
     * Получить индекс
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * Установить индекс
     * @param string $postalCode Индекс
     * @return self
     */
    public function setPostalCode(string $postalCode): self;

    /**
     * Получить код страны
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * Установить код страны
     * @param string $countryCode код страны
     * @return self
     */
    public function setCountryCode(string $countryCode): self;

    /**
     * Получить имя получателя
     * @return string
     */
    public function getClientName(): string;

    /**
     * Установить имя получателя
     * @param string $clientName имя получателя
     * @return self
     */
    public function setClientName(string $clientName): self;
}