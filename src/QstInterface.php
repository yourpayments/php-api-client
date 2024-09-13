<?php declare(strict_types=1);

namespace Ypmn;

interface QstInterface
{
    /**
     * ИНН продавца
     * @return string
     */
    public function getInn(): string;

    /**
     * ИНН продавца
     * @param string $inn
     * @return $this
     */
    public function setInn(string $inn): self;

    /**
     * Данные продавца
     * @return QstSchemaInterface
     */
    public function getSchema(): QstSchemaInterface;

    /**
     * Данные продавца
     * @param QstSchemaInterface $schema
     * @return $this
     */
    public function setSchema(QstSchemaInterface $schema): self;
}
