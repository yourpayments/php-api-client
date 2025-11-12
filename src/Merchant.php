<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Мерчант (продавец, юридическое лицо)
 */
class Merchant implements MerchantInterface
{
    /**
     * Код Продавца (Идентификатор Мерчанта)
     * можно получить в https://secure.ypmn.ru/cpanel/account_settings.php
     * Или в https://sandbox.ypmn.ru/cpanel/account_settings.php для тестов
     * @var string Код Продавца (Идентификатор Мерчанта)
     */
    private string $merchantCode;

    /**
     * Секретный Ключ
     * !НЕ передавать в открытом виде
     * получить в https://secure.ypmn.ru/cpanel/account_settings.php
     * Или в https://sandbox.ypmn.ru/cpanel/account_settings.php для тестов
     @var string Секретный Ключ (!НЕ передавать в открытом виде)
     */
    private string $merchantSecret;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /**
     * @param string $merchantCode Код Продавца (Идентификатор Мерчанта)
     * @param string $merchantSecret Секретный Ключ (!НЕ передавать в открытом виде)
     */
    public function __construct(string $merchantCode, string $merchantSecret)
    {
        $this->setCode($merchantCode);
        $this->setSecret($merchantSecret);
    }

    /** @inheritDoc */
    public function setCode(string $merchantCode): MerchantInterface
    {
        $this->merchantCode = $merchantCode;
        return $this;
    }

    /** @inheritDoc */
    public function getCode(): string
    {
        return $this->merchantCode;
    }

    /** @inheritDoc */
    public function setSecret(string $merchantSecret): MerchantInterface
    {
        $this->merchantSecret = $merchantSecret;
        return $this;
    }

    /** @inheritDoc */
    public function getSecret(): string
    {
        return $this->merchantSecret;
    }
}
