<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Это класс адреса продавца в анкете
 **/
trait QstSchemaCheckableTrait
{
    private bool $checked = false;

    /**
     * @return bool|null
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    /**
     * @param bool|null $checked
     * @return $this
     */
    public function setChecked(bool $checked)
    {
        $this->checked = $checked;
        return $this;
    }
}
