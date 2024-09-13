<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это интерфейс Сабмерчанта (для маркетплейса)
 */
interface MarketplaceSubmerchantInterface
{
    /**
     * Установить Код Сабмерчанта (для маркетплейса)
     * @param string $merchantCode
     * @return $this
     */
    public function setMerchantCode(string $merchantCode) : self;

    /**
     * Получить Код Сабмерчанта (для маркетплейса)
     * @return string
     */
    public function getMerchantCode() : string;

    /**
     * Установить Сумму
     * @param float $amount
     * @return $this
     */
    public function setAmount(float $amount) : self;

    /**
     * Получить Сумму
     * @return float
     */
    public function getAmount() : float;
}
