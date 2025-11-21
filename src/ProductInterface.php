<?php declare(strict_types=1);

namespace Ypmn;

interface ProductInterface
{
    /**
     * Создание Продукта из массива параметров
     * @param array $params параметры товара
     */
    public function __construct(array $params);

    /**
     * Установить Наименование
     * @param string $name Наименование
     * @return $this
     */
    public function setName(string $name) : self;

    /**
     * Получить Наименование
     * @return string Наименование
     */
    public function getName() : string;

    /**
     * Установить Артикул, либо идентификатор
     * @param string $sku Артикул, либо идентификатор
     * @return $this
     */
    public function setSku(string $sku) : self;

    /**
     * Получить Артикул, либо идентификатор
     * @return string Артикул, либо идентификатор
     */
    public function getSku() : string;

    /**
     * Установить Цена за 1 штуку, округление до копейки
     * @param float $unitPrice
     * @return $this Цена за 1 штуку
     * @throws PaymentException
     */
    public function setUnitPrice(float $unitPrice) : self;

    /**
     * Получить Цена за 1 штуку
     * @return float Цена за 1 штуку
     */
    public function getUnitPrice() : float;

    /**
     * Установить Количество
     * @param int $quantity Количество
     * @return $this
     * @throws PaymentException
     */
    public function setQuantity(int $quantity) : self;

    /**
     * Получить Количество
     * @return int Количество
     */
    public function getQuantity() : int;

    /**
     * Установить Ставка НДС, по-умолчанию 20
     * @param int $vat Ставка НДС, по-умолчанию 20
     * @return $this
     */
    public function setVat(int $vat) : self;

    /**
     * Получить Ставка НДС, по-умолчанию 20
     * @return int Ставка НДС, по-умолчанию 20
     */
    public function getVat() : int;

    /**
     * Установить Подитог
     * ВАЖНО: не должно превышать оригинальную стоимость (unitPrice * quantity) продукта при авторизации
     * @param float $amount Подитог
     * @return $this
     */
    public function setAmount(float $amount) : self;

    /**
     * Получить Подитог
     * @return null|float Подитог
     */
    public function getAmount() : ?float;

    /**
     * Получить Единицу измерения (для чеков, например: "кг", "шт", "упаковка 10 шт")
     * @return string Единица измерения
     */
    public function getMeasurementUnit(): string;

    /**
     * Установить Единицу измерения (для чеков, например: 'кг', 'шт', 'упаковка 10 шт')
     * @param string $measurementUnit
     * @return $this
     */
    public function setMeasurementUnit(string $measurementUnit): Product;

    /**
     * Установить Любые доп. сведения
     * @param string $additionalDetails Любые доп. сведения
     * @return $this
     */
    public function setAdditionalDetails(string $additionalDetails) : self;

    /**
     * Получить Любые доп. сведения
     * @return null|string Любые доп. сведения
     */
    public function getAdditionalDetails() : ?string;

    /**
     * Получить сабмерчанта
     * @return MarketplaceSubmerchantInterface|null
     */
    public function getMarketplaceSubmerchant(): ?MarketplaceSubmerchantInterface;

    /**
     * Добавить к Продукту Сабмерчанта
     * (для маркетплейса/сплита/разделения платежа)
     * с помощью Кода Сабмерчанта
     * (его надо получить в личном кабинете)
     * @param string $merchantCode
     * @return $this
     */
    public function setMarketplaceSubmerchantByCode(string $merchantCode) : self;

    /**
     * Получить Продукт в виде массива
     * @return array Продукт в виде массива
     */
    public function arraySerialize(): array;
}
