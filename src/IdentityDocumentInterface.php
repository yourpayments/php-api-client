<?php

declare(strict_types=1);

namespace Ypmn;

interface IdentityDocumentInterface
{
    /**
     * Создание Удостоверения Личности
     * @param int $number Номер документа
     * @param string $type Вид документа
     */
    public function __construct(int $number, string $type);

    /**
     * Получить Номер Документа
     * @return int Номер Документа
     */
    public function getNumber(): int;

    /**
     * Установить Номер Документа
     * @param int $number Номер Документа
     * @return $this Удостоверение Личности
     */
    public function setNumber(int $number): self;

    /**
     * Получить Вид документа
     * @return string Вид документа
     */
    public function getType(): string;

    /**
     * Установить Вид документа
     * @param string $type Вид документа
     * @return $this Удостоверение Личности
     */
    public function setType(string $type): self;

    /**
     * @return array Удостоверение Личности в виде массива
     */
    public function arraySerialize(): array;
}
