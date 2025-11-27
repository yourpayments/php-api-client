<?php declare(strict_types=1);

namespace Ypmn;

interface AuthorizationInterface
{
    /**
     * Установить Cпособ Оплаты (из справочника PaymentMethods.php) || NULL чтобы плательщик выбрал сам
     * @param string|null $paymentMethod Cпособ Оплаты
     * @return AuthorizationInterface
     * @throws PaymentException Ошибка оплаты
     */
    public function setPaymentMethod(?string $paymentMethod): self;

    /**
     * Получить Cпособ Оплаты (из справочника PaymentMethods.php) || NULL чтобы плательщик выбрал сам
     * @return null|string Cпособ Оплаты
     */
    public function getPaymentMethod(): ?string;

    /**
     * Установить Использование платёжной страницы
     * @param null|bool $isUsed Использовать платёжную страницу
     * @return AuthorizationInterface
     */
    public function setUsePaymentPage(?bool $isUsed): self;

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
    public function getUsePaymentPage(): bool;

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
     * @throws PaymentException
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

    /**
     * Получить настройки платёжной страницы
     * @return PaymentPageOptionsInterface
     */
    public function getPaymentPageOptions(): PaymentPageOptionsInterface;

    /**
     * Установить значения для 3DS
     * (только для h2h, при наличии PCI-DSS)
     *
     * Данные по-умолчанию могут быть использованы только для тестов
     *
     * @param int|null $screenHeight высота экрана в пискселях
     * @param int|null $screenWidth ширина экрана в пискселях
     * @param int|null $timezone временная зона в минутах (напр. -180)
     * @param string|null $userAgent строка, описывающая клиента
     * @param string|null $colorDepth цветопередача экрана
     * @param string|null $language язык, напр. 'ru-RU'
     * @param string|null $requestIp IP-адрес клиента
     * @return self
     */
    public function setThreeDSecure(
        int $screenHeight = null,
        int $screenWidth = null,
        int $timezone = null,
        string $userAgent = null,
        string $colorDepth = null,
        string $language = null,
        string $requestIp = null
    ): self;
}
