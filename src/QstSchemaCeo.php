<?php

declare(strict_types=1);

namespace Ypmn;

/*
 * Руководитель продавца в анкете
 */
class QstSchemaCeo implements QstSchemaCeoInterface
{
    private ?string $citizenship = null;
    private string $birthDate;
    private string $birthPlace;
    private QstSchemaIdentityDocInterface $identityDoc;
    private string $registrationAddress;

    /** @inheritdoc */
    public function getCitizenship(): ?string
    {
        return $this->citizenship;
    }

    /** @inheritdoc */
    public function setCitizenship(string $citizenship): self
    {
        $this->citizenship = $citizenship;
        return $this;
    }

    /** @inheritdoc */
    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    /** @inheritdoc */
    public function setBirthDate(string $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /** @inheritdoc */
    public function getBirthPlace(): string
    {
        return $this->birthPlace;
    }

    /** @inheritdoc */
    public function setBirthPlace(string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;
        return $this;
    }

    /** @inheritdoc */
    public function getIdentityDoc(): QstSchemaIdentityDocInterface
    {
        return $this->identityDoc;
    }

    /** @inheritdoc */
    public function setIdentityDoc(QstSchemaIdentityDocInterface $identityDoc): self
    {
        $this->identityDoc = $identityDoc;
        return $this;
    }

    /** @inheritdoc */
    public function getRegistrationAddress(): string
    {
        return $this->registrationAddress;
    }

    /** @inheritdoc */
    public function setRegistrationAddress(string $registrationAddress): self
    {
        $this->registrationAddress = $registrationAddress;
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): ?array
    {
        $array = [
            'citizenship' => $this->getCitizenship(),
            'birthDate' => $this->getBirthDate(),
            'birthPlace' => $this->getBirthPlace(),
            'identityDoc' => $this->getIdentityDoc()->toArray(),
            'registrationAddress' => $this->getRegistrationAddress()
        ];

        return array_filter($array, static fn ($value) => $value !== null);
    }
}