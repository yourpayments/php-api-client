<?php declare(strict_types=1);

namespace Ypmn;

interface QstSchemaCeoInterface extends QstToArrayInterface
{
    /**
     * Гражданство
     * @return string|null
     */
    public function getCitizenship(): ?string;

    /**
     * Гражданство
     * @param string $citizenship
     * @return $this
     */
    public function setCitizenship(string $citizenship): self;

    /**
     * Дата рождения
     * @return string
     */
    public function getBirthDate(): string;

    /**
     * Дата рождения
     * @param string $birthDate
     * @return $this
     */
    public function setBirthDate(string $birthDate): self;

    /**
     * Место рождения
     * @return string
     */
    public function getBirthPlace(): string;

    /**
     * Место рождения
     * @param string $birthPlace
     * @return $this
     */
    public function setBirthPlace(string $birthPlace): self;

    /**
     * Документ удостоверяющий личность
     * @return QstSchemaIdentityDocInterface
     */
    public function getIdentityDoc(): QstSchemaIdentityDocInterface;

    /**
     * Документ удостоверяющий личность
     * @param QstSchemaIdentityDocInterface $identityDoc
     * @return $this
     */
    public function setIdentityDoc(QstSchemaIdentityDocInterface $identityDoc): self;

    /**
     * Адрес регистрации
     * @return string
     */
    public function getRegistrationAddress(): string;

    /**
     * Адрес регистрации
     * @param string $registrationAddress
     * @return $this
     */
    public function setRegistrationAddress(string $registrationAddress): self;
}
