<?php

declare(strict_types=1);

namespace Ypmn;


use JsonSerializable;
use Ypmn\Traits\ProtobufSerializable;

/**
 * Платеж
 */
class Payment implements PaymentInterface, JsonSerializable, TransactionInterface, PaymentDetailsInterface
{
    /** @var string Идентификатор платежа у Мерчанта */
    private string $merchantPaymentReference;

    /** @var string код валюты */
    private string $currency = 'RUB';

    /** @var string URL страницы после успешной оплаты */
    private string $successUrl;

    /** @var string URL страницы после НЕуспешной оплаты */
    private string $failUrl;

    /** @var string Универсальный URL страницы после оплаты */
    private string $returnUrl;

    /** @var AuthorizationInterface Авторизация */
    private AuthorizationInterface $authorization;

    /** @var StoredCredentialsInterface Учетные данные для подписок */
    private StoredCredentialsInterface $storedCredentials;

    /** @var ClientInterface Клиент */
    private ClientInterface $client;

    /** @var Product[] Массив продуктов */
    private array $products;

    /** @var Details|null Данные с расширенными сведениями в парах ключ/значение */
    private ?Details $details = null;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /** @inheritDoc */
    public function setMerchantPaymentReference(string $paymentIdString) : self
    {
        $this->merchantPaymentReference = $paymentIdString;

        return $this;
    }

    /** @inheritDoc */
    public function getMerchantPaymentReference() : string
    {
        return $this->merchantPaymentReference;
    }

    /** @inheritDoc */
    public function setCurrency(?string $currency = 'RUB') : self
    {
        if($currency === null) {
            $currency = 'RUB';
        }

        $this->currency = $currency;

        // TODO: Implement Currency check method.
        return $this;
    }

    /** @inheritDoc */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /** @inheritDoc */
    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    /** @inheritDoc */
    public function setSuccessUrl(string $successUrl): self
    {
        $this->successUrl = $successUrl;

        return $this;
    }

    /** @inheritDoc */
    public function getFailUrl(): string
    {
        return $this->failUrl;
    }

    /** @inheritDoc */
    public function setFailUrl(string $failUrl): self
    {
        $this->failUrl = $failUrl;

        return $this;
    }

    /** @inheritDoc */
    public function setReturnUrl(string $returnUrl) : self
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /** @inheritDoc */
    public function getReturnUrl(): string
    {
        return  $this->returnUrl;
    }

    /** @inheritDoc */
    public function setAuthorization(AuthorizationInterface $authorization) : self
    {
        $this->authorization = $authorization;

        return $this;
    }

    /** @inheritDoc */
    public function getAuthorization() : AuthorizationInterface
    {
        return $this->authorization;
    }

    /** @inheritDoc */
    public function getStoredCredentials(): StoredCredentialsInterface
    {
        if (empty($this->storedCredentials)) {
            $this->storedCredentials = new StoredCredentials();
        }

        return $this->storedCredentials;
    }

    /** @inheritDoc */
    public function setStoredCredentials(StoredCredentialsInterface $storedCredentials): self
    {
        $this->storedCredentials = $storedCredentials;

        return $this;
    }

    /** @inheritDoc */
    public function setClient(ClientInterface $client) : self
    {
        $this->client = $client;

        return $this;
    }

    /** @inheritDoc */
    public function getClient(): ClientInterface
    {
        return $this->client;
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
            $productsArray[] = $product->arraySerialize();
        }

        return $productsArray;
    }

    /** @inheritdoc */
    public function sumProductsAmount() : float
    {
        $sum = 0;
        foreach ($this->getProducts() as $product) {
            if (null === $product->getAmount() && (null === $product->getUnitPrice())) {
                throw new PaymentException('Опишите цены позиций к оплате');
            }

            $sum += $product->getAmount() ?? ($product->getUnitPrice() * $product->getQuantity());
        }

        return $sum;
    }

    /** @inheritdoc */
    public function getDetails(): ?Details
    {
        return $this->details;
    }

    /** @inheritdoc */
    public function setDetails(?Details $details): self
    {
        $this->details = $details;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $storedCredentials = $this->getStoredCredentials()->arraySerialize();

        //TODO: проверка необходимых параметров
        $requestData['merchantPaymentReference'] = $this->getMerchantPaymentReference();
        $requestData['currency']      = $this->getCurrency();
        $requestData['returnUrl']     = $this->getReturnUrl();
        $requestData['authorization'] = $this->getAuthorization()->arraySerialize();

        /* Поле storedCredentials обязательно только при привязке карты */
        if ((bool)$storedCredentials !== false) {
            $requestData['storedCredentials'] = $storedCredentials;
            /* При создании привязки через СБП необходимо 2 поля consentType и subscriptionPurpose */
            if (
                empty($requestData['storedCredentials']['consentType']) === false &&
                $requestData['authorization']['paymentMethod'] === PaymentMethods::FASTER_PAYMENTS
            ) {
                $requestData['storedCredentials']['subscriptionPurpose'] = $this->getStoredCredentials()->getSubscriptionPurpose();
            }
        }

        $requestData['client']   = $this->getClient()->arraySerialize();
        $requestData['products'] = $this->getProductsArray();

        if (!is_null($this->details)) {
            $requestData['details'] = $this->details->toArray();
        }

        $requestData = Std::removeNullValues($requestData);

        return json_encode($requestData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
