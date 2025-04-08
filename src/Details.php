<?php

declare(strict_types=1);

namespace Ypmn;

use Exception;

/**
 * Расширенные данные по транзакции
 */
class Details
{
    /** @var string|SubmerchantReceipt[]|null */
    private $receipts = null;

    /**
     * Получить:
     *  - массив объектов, каждый из которых содержит мерчант код и строку с данными для регистрации чеков
     *  - строку с данными для регистрации чеков (для остальных мерчантов)
     * @return string|SubmerchantReceipt[]|null
     */
    public function getReceipts()
    {
        return $this->receipts;
    }

    /**
     * Установить строку с данными для регистрации чеков
     * @param string|SubmerchantReceipt[]|null $receipts
     * @return $this
     * @throws Exception
     */
    public function setReceipts($receipts): self
    {
        if (is_string($receipts)) {
            json_decode($receipts);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Строка должна быть закодированной в JSON строкой');
            }
        }

        $this->receipts = $receipts;

        return $this;
    }

    /**
     * Преобразовать объект в строку
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        if (is_string($this->getReceipts())) {
            $array += [
                'receipts' => json_decode($this->getReceipts())
            ];
        } else {
            $array += ['receipts' => []];
            /** @var SubmerchantReceipt[] $receipts */
            $receipts = $this->getReceipts();
            foreach ($receipts as $submerchantReceipt) {
                $array['receipts'][] = $submerchantReceipt->toArray();
            }
        }

        return array_filter($array, static fn ($value) => $value !== null);
    }
}