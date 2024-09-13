<?php declare(strict_types=1);

namespace Ypmn;

interface MerchantInterface
{
    /**
     * Установить код мерчанта
     * (Идентификатор продавца в системе Ypmn)
     * @param string $merchantCode
     * @return $this
     */
    public function setCode(string $merchantCode) : self;

    /**
     * Получить код мерчанта
     * @return string
     */
    public function getCode() : string;

    /**
     * Установить секретный ключ
     * @param string $merchantSecret
     * @return $this
     */
    public function setSecret(string $merchantSecret) : self;

    /**
     * Получить секретный ключ
     * (никогда не пересылайте свой ключ в явном виде!)
     * @return string
     */
    public function getSecret() : string;
}
