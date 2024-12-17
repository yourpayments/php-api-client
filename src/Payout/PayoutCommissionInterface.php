<?php declare(strict_types=1);

namespace Ypmn\Payout;

interface PayoutCommissionInterface
{
    /**
     * Получить комиссию продавца
     * @return float
     */
    public function getMerchant(): float;

    /**
     * Установить комиссию продавца
     * @param float $merchant Комиссия продавца
     * @return self
     */
    public function setMerchant(float $merchant): self;

    /**
     * Получить комиссию бенефициара
     * @return float
     */
    public function getPayee(): float;

    /**
     * Установить комиссию бенефициара
     * @param float $payee Комиссия бенефициара
     * @return self
     */
    public function setPayee(float $payee): self;
}