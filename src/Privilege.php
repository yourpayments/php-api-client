<?php

declare(strict_types=1);

namespace Ypmn;

/*
 * API-USRMNG
 */
class Privilege implements ApiCpanelArraySerializeInterface
{
    /**
     * API имя привилегии.
     *
     * @var string
     */
    private string $apiName;

    /**
     * Статус привилегии - включена/выключена.
     *
     * @var boolean
     */
    private bool $isOn;

    public function __construct(string $privilegeApiName, bool $isOn)
    {
        $this->apiName = $privilegeApiName;
        $this->isOn = $isOn;
    }

    /**
     * Возвращает АПИ имя привилегии.
     *
     * @return string
     */
    public function getApiName(): string
    {
        return $this->apiName;
    }

    /**
     * Устанавливает АПИ имя привилегии.
     *
     * @param string $privilegeApiName
     * @return Privilege
     */
    public function setApiName(string $privilegeApiName): self
    {
        $this->apiName = $privilegeApiName;

        return $this;
    }

    /**
     * Возвращает статус привилегии.
     *
     * @return bool
     */
    public function isOn(): bool
    {
        return $this->isOn;
    }

    /**
     * Активировать привилегию.
     *
     * @return Privilege
     */
    public function on(): self
    {
        $this->isOn = true;

        return $this;
    }

    /**
     * Деактивировать привилегию.
     *
     * @return Privilege
     */
    public function off(): self
    {
        $this->isOn = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arraySerialize(): array
    {
        return [$this->apiName => $this->isOn];
    }
}
