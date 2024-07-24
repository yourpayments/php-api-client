<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Почтовый адрес продавца в анкете
 **/
class QstSchemaPostAddress extends QstSchemaAddressAbstract
{
    use QstSchemaCheckableTrait;

    /**
     * @return array|null
     */
    public function toArray(): ?array
    {
        if ($this->isChecked()) {
            return [
                'isEqualToLegalAddress' => true
            ];
        }

        return parent::toArray();
    }
}
