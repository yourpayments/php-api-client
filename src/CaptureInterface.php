<?php declare(strict_types=1);

namespace Ypmn;

interface CaptureInterface
{
    /**
     * Переключить режим отладки
     * (скрипт будет выводить отладочные сообщения)
     * @param bool $isOn
     * @return $this
     */
    public function setDebugMode(bool $isOn) : self;

    /**
     * Переключить режим тестирования Ypmn Sandbox
     * оплата будет перенаправлена на тестовый сервер
     * https://sandbox.payu.ru
     * @param bool $isOn
     * @return $this
     */
    public function setSandboxMode(bool $isOn) : self;

    /**
     * Установить Номер платежа Ypmn
     * Используйте значение из JSON-ответа на запрос на авторизацию платежа (ключ 'payuPaymentReference')
     * @param string $paymentIdString Номер платежа Ypmn
     * @return $this
     */
    public function setYpmnPaymentReference(string $paymentIdString) : self;

    /**
     * Получить Номер платежа Ypmn
     * @return string Номер платежа Ypmn
     */
    public function getYpmnPaymentReference() : string;

    /**
     * Установить сумму исходной операции на авторизацию
     * ВАЖНО: должна быть равна сумме исходной операции на авторизацию
     * @param float $originalAmount Сумма исходной операции на авторизацию
     * @return $this
     */
    public function setOriginalAmount(float $originalAmount) : self;

    /**
     * Получить сумму исходной операции на авторизацию
     * @return float Cумма исходной операции на авторизацию
     */
    public function getOriginalAmount() : float;

    /**
     * Установить сумму списания
     * ВАЖНО: не должна превышать сумму исходной операции на авторизацию
     * @param float $amount Сумма списания
     * @return $this
     */
    public function setAmount(float $amount) : self;

    /**
     * Получить сумму списания
     * @return float Cумма списания
     */
    public function getAmount() : float;

    /**
     * Установить Код валюты (например, RUB)
     * формат кодов валюты ISO 4217
     * (https://en.wikipedia.org/wiki/ISO_4217)
     * @param string $currency Код валюты
     * @return $this
     */
    public function setCurrency(string $currency) : self;

    /**
     * Получить Код валюты
     * @return string Код валюты
     */
    public function getCurrency() : string;
}
