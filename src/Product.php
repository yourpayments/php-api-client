<?php

declare(strict_types = 1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Продукт или Услуга
 * (позиция на оплату)
 */
class Product implements ProductInterface
{
    /**
     * @var string Наименование
     */
    private string $name;

    /**
     * @var string Артикул, либо идентификатор
     */
    private string $sku;

    /**
     * @var float Цена за 1 штуку
     */
    private float $unitPrice;

    /**
     * @var int Количество
     */
    private int $quantity;

    /**
     * @var int Ставка НДС
     */
    private int $vat = 20;

    /**
     * @var float Подитог
     */
    private float $amount;

    /**
     * @var string Единица измерения (для чеков)
     */
    private string $measurementUnit;

    /**
     * @var string Любые доп. сведения
     */
    private string $additionalDetails;

    /** @var MarketplaceSubmerchantInterface|null Сабмерчант (для маркетплейса) */
    private ?MarketplaceSubmerchantInterface $marketplaceSubmerchant = null;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /** @inheritDoc */
    public function __construct(array $params=[])
    {
        if (isset($params['name'])) {
            $this->setName($params['name']);
        }
        if (isset($params['sku'])) {
            $this->setSku($params['sku']);
        }
        if (isset($params['unitPrice'])) {
            $this->setUnitPrice($params['unitPrice']);
        }
        if (isset($params['quantity'])) {
            $this->setQuantity($params['quantity']);
        }
        if (isset($params['vat'])) {
            $this->setVat($params['vat']);
        }
        if (isset($params['amount'])) {
            $this->setAmount($params['amount']);
        }
        if (isset($params['merchantCode'])) {
            $this->setMarketplaceSubmerchantByCode($params['merchantCode']);
        }
    }

    /** @inheritDoc */
    public function getName(): string
    {
        return $this->name;
    }

    /** @inheritDoc */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /** @inheritDoc */
    public function getSku(): string
    {
        return $this->sku;
    }

    /** @inheritDoc */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    /** @inheritDoc */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /** @inheritDoc */
    public function setUnitPrice(float $unitPrice): self
    {
        if ($unitPrice <= 0) {
            throw new PaymentException('Нулевая цена не принимается');
        }
        $this->unitPrice = round($unitPrice, 2, PHP_ROUND_HALF_UP);

        return $this;
    }

    /** @inheritDoc */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /** @inheritDoc */
    public function setQuantity(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new PaymentException('Нулевое количество не принимается');
        }

        $this->quantity = $quantity;

        return $this;
    }

    /** @inheritDoc */
    public function getVat(): int
    {
        return $this->vat;
    }

    /** @inheritDoc */
    public function setVat(int $vat): self
    {
        $this->vat = $vat;
        return $this;
    }

    /** @inheritDoc */
    public function getAmount(): ?float
    {
        return ($this->amount ?? null);
    }

    /** @inheritDoc */
    public function setAmount(float $amount): self
    {
        //TODO: ВАЖНО: не должно превышать оригинальную стоимость (unitPrice * quantity) продукта при авторизации
        $this->amount = round($amount, 2, PHP_ROUND_HALF_UP);
        return $this;
    }

    /** @inheritDoc */
    public function getMeasurementUnit(): string
    {
        return $this->measurementUnit;
    }

    /** @inheritDoc */
    public function setMeasurementUnit(string $measurementUnit): Product
    {
        $this->measurementUnit = $measurementUnit;

        return $this;
    }

    /** @inheritDoc */
    public function getAdditionalDetails(): ?string
    {
        return ($this->additionalDetails ?? null);
    }

    /** @inheritDoc */
    public function setAdditionalDetails(string $additionalDetails): self
    {
        $this->additionalDetails = $additionalDetails;
        return $this;
    }

    /** @inheritDoc */
    public function getMarketplaceSubmerchant(): ?MarketplaceSubmerchantInterface
    {
        return $this->marketplaceSubmerchant;
    }

    /** @inheritDoc */
    public function setMarketplaceSubmerchantByCode(string $merchantCode): self
    {
        $this->marketplaceSubmerchant = new MarketplaceSubmerchant($merchantCode);

        return $this;
    }

    /** @inheritDoc */
    public function arraySerialize(): array
    {
        $resultArray = [
            'name'              => $this->getName(),
            'sku'               => $this->getSku(),
            'unitPrice'         => (null !== $this->getUnitPrice() ? number_format($this->getUnitPrice(), 2,'.','') : null),
            'quantity'          => $this->getQuantity(),
            'additionalDetails' => $this->getAdditionalDetails(),
            'amount'            => (null !== $this->getAmount() ? number_format($this->getAmount(), 2,'.','') : null),
            'vat'               => $this->getVat(),
        ];

        if (null !== $this->marketplaceSubmerchant) {
            $resultArray['marketplace']['version'] = 1;
            $resultArray['marketplace']['merchantCode'] = $this->marketplaceSubmerchant->getMerchantCode();
        }

        return $resultArray;
    }
}
