<?php

declare(strict_types = 1);

namespace Ypmn;

interface QstInterface
{
    /**
     * @return string
     */
    public function getInn(): string;

    /**
     * @param string $inn
     * @return $this
     */
    public function setInn(string $inn): self;

    /**
     * @return QstSchemaInterface
     */
    public function getSchema(): QstSchemaInterface;

    /**
     * @param QstSchemaInterface $schema
     * @return $this
     */
    public function setSchema(QstSchemaInterface $schema): self;
}
