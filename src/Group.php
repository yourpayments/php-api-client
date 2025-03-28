<?php

declare(strict_types=1);

namespace Ypmn;

use function array_filter;
use function array_merge;

/*
 * API-USRMNG
 */
class Group implements ApiCpanelArraySerializeInterface
{
    /**
     * API имя группы.
     *
     * @var string|null
     */
    private ?string $apiName = null;

    /**
     * Массив привилегий.
     *
     * @var array<Privilege>
     */
    private array $privileges = [];

    /**
     * Возвращает АПИ имя группы.
     *
     * @return string|null
     */
    public function getApiName(): ?string
    {
        return $this->apiName;
    }

    /**
     * Устанавливает АПИ имя группы.
     *
     * @param string|null $groupApiName
     * @return Group
     */
    public function setApiName(?string $groupApiName): self
    {
        $this->apiName = $groupApiName;

        return $this;
    }

    /**
     * Возвращает набор привилегии в группе.
     *
     * @return array<Privilege>
     */
    public function getPrivileges(): array
    {
        return $this->privileges;
    }

    /**
     * Устанавливает набор привилегий в группе.
     * Имеющиеся привилегии группы перезаписываются.
     *
     * @param array<Privilege> $privileges
     * @return Group
     */
    public function setPrivileges(array $privileges): self
    {
        $arrayPrivileges = array_filter($privileges, fn($privilege) => $privilege instanceof Privilege);
        $this->privileges = $arrayPrivileges;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arraySerialize(): array
    {

        $serializedGroup = [];

        if (!empty($this->apiName)) {
            $serializedGroup[$this->apiName] = [];
            foreach ($this->privileges as $privilege) {
                $serializedGroup[$this->apiName] =
                    array_merge($serializedGroup[$this->apiName], $privilege->arraySerialize());
            }
        }

        return $serializedGroup;
    }
}
