<?php

namespace Ypmn;

interface CardDetailsInterface extends DetailsInterface
{
    /**
     * Установить Месяц прекращения действия Карты
     * @param int $expiryMonth Месяц прекращения действия Карты
     * @return $this
     * @throws PaymentException
     */
    public function setExpiryMonth(int $expiryMonth) : self;

    /**
     * Получить Месяц прекращения действия Карты
     * @return int
     */
    public function getExpiryMonth() : int;

    /**
     * Установить Год прекращения действия Карты
     * @param int $expiryYear Год прекращения действия Карты
     * @return $this
     * @throws PaymentException
     */
    public function setExpiryYear(int $expiryYear) : self;

    /**
     * Получить Год прекращения действия Карты
     * @return int Год прекращения действия Карты
     */
    public function getExpiryYear() : int;

    /**
     * Установить CVV Карты
     * @param string $cvv CVV Карты
     * @return $this
     */
    public function setCvv(string $cvv) : self;

    /**
     * Получить CVV Карты
     * @return string CVV Карты
     */
    public function getCvv() : string;

    /**
     * Установить Имя Владельца Карты
     * @param string $owner Имя Владельца Карты
     * @return $this
     */
    public function setOwner(string $owner) : self;

    /**
     * Получить Имя Владельца Карты
     * @return string Имя Владельца Карты
     */
    public function getOwner() : string;

    /**
     * Установить Время набора Номера Карты (сек)
     * @param int $timeSpentTypingNumber Время набора Номера Карты (сек)
     * @return $this
     */
    public function setTimeSpentTypingNumber(int $timeSpentTypingNumber) : self;

    /**
     * Получить Время набора номера Карты (сек)
     * @return int Время набора Номера Карты (сек)
     */
    public function getTimeSpentTypingNumber() : int;

    /**
     * Установить Время набора Имени Владельца Карты (сек)
     * @param int $timeSpentTypingOwner Время набора Имени Владельца Карты (сек)
     * @return $this
     */
    public function setTimeSpentTypingOwner(int $timeSpentTypingOwner) : self;

    /**
     * Получить Время набора Имени Владельца Карты
     * @return int Время набора Имени Владельца Карты (сек)
     */
    public function getTimeSpentTypingOwner() : int;

    /**
     * Установить BIN (Bank Identification Number)
     * @param int $bin BIN (Bank Identification Number)
     * @return $this
     */
    public function setBin(int $bin) : self;

    /**
     * Получить BIN (Bank Identification Number)
     * @return int BIN (Bank Identification Number)
     */
    public function getBin() : int;

    /**
     * Установить PAN (Permanent Account Number)
     * @param string $pan PAN (Permanent Account Number)
     * @return $this
     */
    public function setPan(string $pan) : self;

    /**
     * Получить PAN (Permanent Account Number)
     * @return string PAN (Permanent Account Number)
     */
    public function getPan() : string;

    /**
     * Установить Тип Карты (например, Visa, MIR)
     * @param string $type Тип Карты
     * @return $this
     */
    public function setType(string $type) : self;

    /**
     * Получить Тип Карты (например, Visa, MIR)
     * @return string Тип Карты
     */
    public function getType() : string;

    /**
     * Установить Банк, выпустивший карту
     * @param string $cardIssuerBank Банк, выпустивший карту
     * @return $this
     */
    public function setCardIssuerBank(string $cardIssuerBank) : self;

    /**
     * Получить Банк, выпустивший карту
     * @return string Банк, выпустивший карту
     */
    public function getCardIssuerBank() : string;

    /**
     * Получить Год Карты
     * @return int Год Карты
     */
    public function getYear(): int;

    /**
     *
     * Установить Год Карты
     * @param int $year Год Карты
     * @return $this Детали Карты
     */
    public function setYear(int $year): self;
}
