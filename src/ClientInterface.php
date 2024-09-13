<?php declare(strict_types=1);

namespace Ypmn;

interface ClientInterface
{
    /**
     * Установить биллинговую информацию
     * @param BillingInterface $billing
     * @return ClientInterface
     */
    public function setBilling(BillingInterface $billing) : self;

    /**
     * Получить биллинговую информацию
     * @return BillingInterface
     */
    public function getBilling() : BillingInterface;

    /**
     * Установить IP текущего посетителя
     * @return ClientInterface
     */
    public function setCurrentClientIp() : self;

    /**
     * Установить IP посетителя в явном виде
     * @param string $clientIp
     * @return $this
     */
    public function setClientIp(string $clientIp) : self;

    /**
     * Получить IP
     * @return string
     */
    public function getClientIp() : ?string;

    /**
     * Установить текущее Время оплаты
     * @return $this
     */
    public function setCurrentClientTime() : self;

    /**
     * Установить Время оплаты в явном виде
     * @param string $clientTime
     * @return $this
     */
    public function setClientTime(string $clientTime) : self;

    /**
     * Получить Время оплаты
     * @return ?string
     */
    public function getClientTime() : ?string;

    /**
     * Установить Подробности Доставки
     * @param DeliveryInterface $delivery Подробности Доставки
     * @return $this
     */
    public function setDelivery(DeliveryInterface $delivery) : self;

    /**
     * Получить Подробности Доставки
     * @return null|DeliveryInterface Подробности Доставки
     */
    public function getDelivery() : ?DeliveryInterface;

    /**
     * Получить Язык общения с Клиентом
     * @return null|string Язык общения с Клиентом
     */
    public function getCommunicationLanguage() : ?string;

    /**
     * Установить Язык общения с Клиентом
     * @param string $communicationLanguage Язык общения с Клиентом
     * @return $this
     */
    public function setCommunicationLanguage(string $communicationLanguage) : self;

    /**
     * Преобразовать в массив
     * @return mixed объект в виде массива
     */
    public function arraySerialize();
}
