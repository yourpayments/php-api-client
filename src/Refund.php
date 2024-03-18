<?php

namespace Ypmn;

use JsonSerializable;

/**
 * Запрос на возврат средств
 */
class Refund implements RefundInterface, JsonSerializable, TransactionInterface
{
    /**
     * @var string Номер платежа Ypmn
     */
    private string $payuPaymentReference;

    /**
     * @var float Cумма исходной операции на авторизацию
     */
    private float $originalAmount;

    /**
     * @var float Сумма списания
     */
    private float $amount;

    /**
     * @var string Валюта
     */
    private string $currency;

    /** @var MarketplaceSubmerchant[] */
    private array $marketplaceSubmerchants = [];

    /** @var Product[] Массив продуктов */
    private array $products;

    private bool $skipCheckSkuAmount = false;

    /**
     * @inheritDoc
     */
    public function setDebugMode(bool $isOn) : self
    {
        $this->debugMode = $isOn;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSandboxMode(bool $isOn) : self
    {
        $this->sandboxMode = $isOn;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setYpmnPaymentReference(string $paymentIdString): RefundInterface
    {
        $this->payuPaymentReference = $paymentIdString;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getYpmnPaymentReference(): string
    {
        return $this->payuPaymentReference;
    }

    /**
     * @inheritDoc
     */
    public function setOriginalAmount(float $originalAmount): RefundInterface
    {
        $this->originalAmount = $originalAmount;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOriginalAmount(): float
    {
        return $this->originalAmount;
    }

    /**
     * @inheritDoc
     */
    public function setAmount(float $amount): RefundInterface
    {
        if ($amount > $this->originalAmount) {
            throw new PaymentException('Списываемая сумма не должна быть больше суммы авторизации');
        }
        $this->amount = $amount;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function setCurrency(string $currency): RefundInterface
    {
        // TODO: Implement Currency check method (in Currency Class).
        // TODO: Create Class Currency, pass Currency object to the constructors... (Payment, Capture and Refund Classes)
        $this->currency = $currency;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isSkipCheckSkuAmount(): bool
    {
        return $this->skipCheckSkuAmount;
    }

    /**
     * @param bool $skipCheckSkuAmount
     */
    public function setSkipCheckSkuAmount(bool $skipCheckSkuAmount): self
    {
        $this->skipCheckSkuAmount = $skipCheckSkuAmount;

        return $this;
    }

    /** @inheritDoc */
    public function addProduct(ProductInterface $product) : self
    {
        $this->products[] = $product;

        return $this;
    }

    /** @inheritDoc */
    public function getProducts(): array
    {
        return $this->products;
    }

    /** @inheritDoc */
    public function getProductsArray(): array
    {
        $productsArray = [];
        foreach ($this->getProducts() as $product) {
            $productsArray[] = [
                'sku' => $product->getSku(),
                'quantity' => $product->getQuantity(),
                'amount' => $product->getAmount(),
            ];
        }

        return $productsArray;
    }

    /**
     * @inheritDoc
     * @throws PaymentException
     */
    public function addMarketPlaceSubmerchant(string $merchantCode, float $amount): self
    {
        $this->marketplaceSubmerchants[$merchantCode] = new MarketplaceSubmerchant($merchantCode, $amount);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        //TODO: проверка необходимых параметров
        $requestData = [
            'payuPaymentReference'	=> $this->getYpmnPaymentReference(),
            'originalAmount'	=> $this->getOriginalAmount(),
            'amount'	=> $this->getAmount(),
            'currency' => $this->getCurrency()
        ];

        if ($this->skipCheckSkuAmount) {
            $requestData ['skipCheckSkuAmount'] = true;
        }

        if (count($this->products) > 0) {
            $requestData['products'] = $this->getProductsArray();
        }

        if (count($this->marketplaceSubmerchants) > 0) {
            foreach ($this->marketplaceSubmerchants as $marketplaceSubmerchant) {
                $requestData['marketplaceV1'][] = (object) [
                    'amount' => $marketplaceSubmerchant->getAmount(),
                    'merchant' => $marketplaceSubmerchant->getMerchantCode(),
                ];
            }
        }

        return json_encode($requestData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
