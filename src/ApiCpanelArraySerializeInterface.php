<?php

declare(strict_types=1);

namespace Ypmn;

interface ApiCpanelArraySerializeInterface
{
    /**
     * Возвращает сериализованное в массив представление объекта
     * @return array
     */
    public function arraySerialize(): array;
}
