<?php

declare(strict_types=1);

namespace Ypmn;

use Exception;

/**
 * Данные для регистрации чека для сабмерчанта (для запросов маркетплейса)
 */
class SubmerchantReceipt
{
    private string $merchantCode;
    private string $receipt;

    /**
     * @param string $merchantCode
     * @param string $receipt
     */
    public function __construct(string $merchantCode, string $receipt)
    {
        $this->merchantCode = $merchantCode;
        $this->receipt = $receipt;
    }

    /**
     * Получить мерчант код
     * @return string
     */
    public function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    /**
     * Установить мерчант код
     * @param string $merchantCode
     * @return $this
     */
    public function setMerchantCode(string $merchantCode): self
    {
        $this->merchantCode = $merchantCode;
        return $this;
    }

    /**
     * Получить строку с данными для регистрации чеков
     * @return string
     */
    public function getReceipt(): string
    {
        return $this->receipt;
    }

    /**
     * Установить строку с данными для регистрации чеков
     * @param string $receipt
     * @return $this
     * @throws Exception
     */
    public function setReceipt(string $receipt): self
    {
        json_decode($receipt);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Значение должно быть закодированной в JSON строкой');
        }

        $this->receipt = $receipt;
        return $this;
    }

    /**
     * Вернуть массив данных
     * @return array
     */
    public function toArray(): array
    {
        return [
            'merchantCode' => $this->merchantCode,
            'receipt' => $this->receipt,
        ];
    }
}