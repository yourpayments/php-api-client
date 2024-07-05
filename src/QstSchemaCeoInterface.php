<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaCeoInterface extends QstToArrayInterface
{
    /**
     * @return string|null
     */
    public function getCitizenship(): ?string;

    /**
     * @param string $citizenship
     * @return $this
     */
    public function setCitizenship(string $citizenship): self;

    /**
     * @return string
     */
    public function getBirthDate(): string;

    /**
     * @param string $birthDate
     * @return $this
     */
    public function setBirthDate(string $birthDate): self;

    /**
     * @return string
     */
    public function getBirthPlace(): string;

    /**
     * @param string $birthPlace
     * @return $this
     */
    public function setBirthPlace(string $birthPlace): self;

    /**
     * @return QstSchemaIdentityDocInterface
     */
    public function getIdentityDoc(): QstSchemaIdentityDocInterface;

    /**
     * @param QstSchemaIdentityDocInterface $identityDoc
     * @return $this
     */
    public function setIdentityDoc(QstSchemaIdentityDocInterface $identityDoc): self;

    /**
     * @return string
     */
    public function getRegistrationAddress(): string;

    /**
     * @param string $registrationAddress
     * @return $this
     */
    public function setRegistrationAddress(string $registrationAddress): self;
}
