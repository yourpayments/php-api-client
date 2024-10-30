<?php

declare(strict_types=1);

namespace Ypmn;

/*
 * API-USRMNG
 */
class Role implements ApiCpanelArraySerializeInterface
{
    /**
     * Системный идентификатор роли.
     *
     * @var int|null
     */
    private ?int $id = null;

    /**
     * Системное имя роли.
     *
     * @var string|null
     */
    private ?string $name = null;

    /**
     * Описание роли.
     *
     * @var string|null
     */
    private ?string $description = null;

    /**
     * Системный код мерчанта - владелеца роли.
     * При пустом значении - роль устанавливается мерчанту, инициирующему запрос.
     *
     * @var string|null
     */
    private ?string $owner = null;

    /**
     * Набор групп с привилегиями для группы.
     *
     * @var array<Group>
     */
    private array $groupOfPrivileges = [];

    /**
     * Получить системный индентификатор роли.
     * NULL для новой роли.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Установить системный идентификатор роли.
     *
     * @param int|null $id
     * @return Role
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Получить системное имя роли.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установить системное имя роли.
     *
     * @param string|null $name
     * @return Role
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Получить описание роли.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Установить описание роли.
     *
     * @param string|null $description
     * @return Role
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Получить системный код мерчанта - владельца роли.
     *
     * @return string|null
     */
    public function getOwner(): ?string
    {
        return $this->owner;
    }

    /**
     * Установить системный код мерчанта - владельца роли.
     *
     * @param string|null $owner
     * @return Role
     */
    public function setOwner(?string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Получить набор групп привилегий, определённых для роли.
     *
     * @return array
     */
    public function getGroupOfPrivileges(): array
    {
        return $this->groupOfPrivileges;
    }

    /**
     * Установить набор групп привилегий, определённых для роли.
     *
     * @param array $groupOfPrivileges
     * @return Role
     */
    public function setGroupOfPrivileges(array $groupOfPrivileges): self
    {
        $arrayGroupsOfPrivileged = array_filter($groupOfPrivileges, fn($group) => $group instanceof Group);
        $this->groupOfPrivileges = $arrayGroupsOfPrivileged;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arraySerialize(): array
    {
        $serializedRole =  array_filter(
            [
                'roleName' => $this->name,
                'roleDescription' => $this->description,
                'merchant' => $this->owner,
                'roleID' => (string) $this->id,
            ]
        );

        $serializedGroups = [];
        foreach ($this->groupOfPrivileges as $groupOfPrivilege) {
            $serializedGroups =
                array_merge($serializedGroups, $groupOfPrivilege->arraySerialize());
        }

        $serializedRole['privilege'] = $serializedGroups;

        return $serializedRole;
    }
}
