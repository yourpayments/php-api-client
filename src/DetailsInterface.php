<?php

namespace Ypmn;

interface DetailsInterface
{
    /**
     * Установить Номер карты
     * @param string $number Номер карты
     * @return $this
     */
    public function setNumber(string $number) : self;

    /**
     * Получить Номер карты
     * @return string Номер карты
     */
    public function getNumber() : string;
}
