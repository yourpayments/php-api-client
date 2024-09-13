<?php declare(strict_types=1);

namespace Ypmn;

trait QstSchemaCheckableTrait
{
    private bool $checked = false;

    /**
     * Получить свойство isChecked для поля в анкете
     * @return bool|null
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    /**
     * Установить свойство isChecked для поля в анкете
     * @param bool|null $checked
     * @return $this
     */
    public function setChecked(bool $checked)
    {
        $this->checked = $checked;
        return $this;
    }
}
