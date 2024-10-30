<?php

declare(strict_types=1);

namespace Ypmn;

use function array_filter;

/*
 * API-USRMNG
 */
class CpanelUser implements ApiCpanelArraySerializeInterface
{
    public const USER_STATUS_ENABLED = 'ENABLED';
    public const USER_STATUS_DISABLED = 'DISABLED';
    public const USER_STATUS_DELETED = 'DELETED';

    /**
     * Системный идентификатор пользователя.
     *
     * @var int|null
     */
    private ?int $id = null;

    /**
     * Имя пользователя.
     *
     * @var string|null
     */
    private ?string $firstName = null;

    /**
     * Фамилия пользователя.
     *
     * @var string|null
     */
    private ?string $lastName = null;

    /**
     * Email пользователя.
     *
     * @var string|null
     */
    private ?string $email = null;

    /**
     * Пароль пользователя.
     *
     * @var string|null
     */
    private ?string $password = null;

    /**
     * Статус пользователя включен/выключен/удалён.
     *
     * @var string|null
     */
    private ?string $status = null;

    /**
     * Набор ролей пользователя.
     *
     * @var array<Role>
     */
    private array $roles = [];

    /**
     * Получить ID пользователя.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Установить ID пользователя
     *
     * @param int|null $id
     * @return CpanelUser
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Получить имя пользователя.
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Установить имя пользователя.
     *
     * @param string|null $firstName
     * @return CpanelUser
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Получить фамилию пользователя.
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Установить фамилию пользователя.
     *
     * @param string|null $lastName
     * @return CpanelUser
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Получить email пользователя.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Установить email пользователя.
     *
     * @param string|null $email
     * @return CpanelUser
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Получить пароль пользователя.
     * Только для нового пользователя, которому был установлен пароль
     * или при смене пароля.
     * Поле актуально до сохранения пользователя в БД.
     * Для пользователей созданных ранее и полученных из БД, всегда возвращается null.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Установить пароль пользователя.
     *
     * @param string|null $password
     * @return CpanelUser
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Получить статус пользователя.
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Включить пользователя.
     *
     * @return CpanelUser
     */
    public function enable(): self
    {
        $this->status = self::USER_STATUS_ENABLED;

        return $this;
    }

    /**
     * Выключить пользователя.
     *
     * @return CpanelUser
     */
    public function disable(): self
    {
        $this->status = self::USER_STATUS_DISABLED;

        return $this;
    }

    /**
     * Отметить пользователя удаленным.
     *
     * @return CpanelUser
     */
    public function delete(): self
    {
        $this->status = self::USER_STATUS_DELETED;

        return $this;
    }

    /**
     * Получить роли пользователя.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Установить роли пользователя.
     *
     * @param array $roles
     * @return CpanelUser
     */
    public function setRoles(array $roles): self
    {
        $arrayRoles = array_filter($roles, fn($role) => $role instanceof Role);
        $this->roles = $arrayRoles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arraySerialize(): array
    {
        $serializedUser =  array_filter(
            [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'emailUser' => $this->email,
                'userID' => (string) $this->id,
                'status' => $this->status,
                'password' => $this->password,
            ]
        );
        $serializedRoles = [];
        foreach ($this->roles as $role) {
            if (empty($roleId = $role->getId()) || empty($merchantCode = $role->getOwner())) {
                continue;
            }
            $serializedRoles[] = ['merchantCode' => $merchantCode, 'roleID' => (string) $roleId];
        }

        $serializedUser['roles'] = $serializedRoles;

        return $serializedUser;
    }
}
