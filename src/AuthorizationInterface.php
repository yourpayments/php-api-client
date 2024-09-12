<?php

namespace Ypmn;

interface AuthorizationInterface
{
    /**
     * Установить Cпособ оплаты (из справочника)
     * @param string $paymentMethod Cпособ оплаты (из справочника)
     * @return AuthorizationInterface
     * @throws PaymentException Ошибка оплаты
     */
    public function setPaymentMethod(string $paymentMethod) : self;

    /**
     * Получить Cпособ оплаты (из справочника)
     * @return null|string Cпособ оплаты (из справочника)
     */
    public function getPaymentMethod(): ?string;

    /**
     * Установить Использование платёжной страницы
     * @param bool $isUsed Использовать платёжную страницу
     * @return AuthorizationInterface
     */
    public function setUsePaymentPage(bool $isUsed) : self;

    /**
     * Получить Данные Карты
     * @return CardDetailsInterface Данные Карты
     */
    public function getCardDetails(): ?CardDetailsInterface;

    /**
     * Установить Данные Карты
     * @param CardDetailsInterface $cardDetails
     * @return Authorization
     */
    public function setCardDetails(CardDetailsInterface $cardDetails): self;

    /**
     * Получить Использование платёжной страницы
     * @return bool Использование платёжной страницы
     */
    public function getUsePaymentPage() : bool;

    /**
     * Получить Токен мерчанта
     * @return MerchantTokenInterface|null Токен мерчанта
     */
    public function getMerchantToken(): ?MerchantTokenInterface;

    /**
     * Установить Токен мерчанта
     * @param MerchantTokenInterface|null $merchantToken Токен мерчанта
     * @return $this
     */
    public function setMerchantToken(?MerchantTokenInterface $merchantToken): self;

    /**
     * Установить Одноразовый Токен
     * @param OneTimeUseToken|null $oneTimeUseToken Одноразовый Токен
     * @return self
     */
    public function setOneTimeUseToken(?OneTimeUseToken $oneTimeUseToken): self;

    /**
     * Получить Одноразовый Токен
     * @return OneTimeUseToken|null Одноразовый Токен
     */
    public function getOneTimeUseToken(): ?OneTimeUseToken;

    /**
     * Установить настройки платёжной страницы
     * @param paymentPageOptionsInterface $paymentPageOptions
     * @return $this
     */
    public function setPaymentPageOptions(PaymentPageOptionsInterface $paymentPageOptions): self;

    public function getPaymentPageOptions(): PaymentPageOptionsInterface;
}
