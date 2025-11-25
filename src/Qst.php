<?php

declare(strict_types=1);

namespace Ypmn;

use JsonSerializable;

/**
 * Анкета продавца
 **/
class Qst implements QstInterface, JsonSerializable
{
    private string $inn;
    private QstSchemaInterface $schema;

    /** @inheritdoc */
    public function getInn(): string
    {
        return $this->inn;
    }

    /** @inheritdoc */
    public function setInn(string $inn): self
    {
        $this->inn = $inn;
        return $this;
    }

    /** @inheritdoc */
    public function getSchema(): QstSchemaInterface
    {
        return $this->schema;
    }

    /** @inheritdoc */
    public function setSchema(QstSchemaInterface $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        $requestData = [
            'inn' => $this->getInn(),
            'schema' => $this->getSchema()->toArray()
        ];

        return json_encode($requestData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
