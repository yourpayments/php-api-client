<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Данные продавца в анкете
 **/
class QstSchema implements QstSchemaInterface
{
    private ?string $foreignName = null;
    private array $phones = [];
    private array $emails = [];
    private QstSchemaAddressInterface $legalAddress;
    private ?QstSchemaAddressInterface $postAddress = null;
    private QstSchemaAddressInterface $actualAddress;
    private ?QstSchemaCeoInterface $ceo = null;
    /** @var QstSchemaOwnerInterface[] */
    private array $owners = [];
    private ?string $boardOfDirectors = null;
    private ?string $managementBoard = null;
    private ?string $otherManagementBodies = null;
    private ?string $addressLocation = null;
    private ?string $birthDate = null;
    private ?string $birthPlace = null;
    private ?QstSchemaIdentityDocInterface $identityDoc = null;
    /** @var QstSchemaBankAccountInterface[] */
    private array $bankAccounts = [];
    private ?string $license = null;
    private ?string $actionInFavor = null;
    private ?string $commission = null;
    private array $additionalFields = [];

    /** @inheritdoc */
    public function getForeignName(): ?string
    {
        return $this->foreignName;
    }

    /** @inheritdoc */
    public function setForeignName(string $foreignName): self
    {
        $this->foreignName = $foreignName;
        return $this;
    }

    /** @inheritdoc */
    public function getPhones(): array
    {
        return $this->phones;
    }

    /** @inheritdoc */
    public function addPhone(string $phone): self
    {
        $this->phones[] = compact('phone');
        return $this;
    }

    /** @inheritdoc */
    public function getEmails(): array
    {
        return $this->emails;
    }

    /** @inheritdoc */
    public function addEmail(string $email): self
    {
        $this->emails[] = compact('email');
        return $this;
    }

    /** @inheritdoc */
    public function getLegalAddress(): QstSchemaAddressInterface
    {
        return $this->legalAddress;
    }

    /** @inheritdoc */
    public function setLegalAddress(QstSchemaAddressInterface $legalAddress): self
    {
        $this->legalAddress = $legalAddress;
        return $this;
    }

    /** @inheritdoc */
    public function getPostAddress(): ?QstSchemaAddressInterface
    {
        return $this->postAddress;
    }

    /** @inheritdoc */
    public function setPostAddress(QstSchemaAddressInterface $postAddress): self
    {
        $this->postAddress = $postAddress;
        return $this;
    }

    /** @inheritdoc */
    public function getActualAddress(): QstSchemaAddressInterface
    {
        return $this->actualAddress;
    }

    /** @inheritdoc */
    public function setActualAddress(QstSchemaAddressInterface $actualAddress): self
    {
        $this->actualAddress = $actualAddress;
        return $this;
    }

    /** @inheritdoc */
    public function getCeo(): ?QstSchemaCeoInterface
    {
        return $this->ceo;
    }

    /** @inheritdoc */
    public function setCeo(QstSchemaCeoInterface $ceo): self
    {
        $this->ceo = $ceo;
        return $this;
    }

    /** @inheritdoc */
    public function getOwners(): array
    {
        return $this->owners;
    }

    /** @inheritdoc */
    public function addOwner(QstSchemaOwnerInterface $owner): self
    {
        $this->owners[] = $owner;
        return $this;
    }

    /** @inheritdoc */
    public function getBoardOfDirectors(): ?string
    {
        return $this->boardOfDirectors;
    }

    /** @inheritdoc */
    public function setBoardOfDirectors(string $boardOfDirectors): self
    {
        $this->boardOfDirectors = $boardOfDirectors;
        return $this;
    }

    /** @inheritdoc */
    public function getManagementBoard(): ?string
    {
        return $this->managementBoard;
    }

    /** @inheritdoc */
    public function setManagementBoard(string $managementBoard): self
    {
        $this->managementBoard = $managementBoard;
        return $this;
    }

    /** @inheritdoc */
    public function getOtherManagementBodies(): ?string
    {
        return $this->otherManagementBodies;
    }

    /** @inheritdoc */
    public function setOtherManagementBodies(string $otherManagementBodies): self
    {
        $this->otherManagementBodies = $otherManagementBodies;
        return $this;
    }

    /** @inheritdoc */
    public function getAddressLocation(): ?string
    {
        return $this->addressLocation;
    }

    /** @inheritdoc */
    public function setAddressLocation(string $addressLocation): self
    {
        $this->addressLocation = $addressLocation;
        return $this;
    }

    /** @inheritdoc */
    public function getBirthDate(): ?string
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
    public function getBirthPlace(): ?string
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
    public function getIdentityDoc(): ?QstSchemaIdentityDocInterface
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
    public function getBankAccounts(): array
    {
        return $this->bankAccounts;
    }

    /** @inheritdoc */
    public function addBankAccount(QstSchemaBankAccountInterface $bankAccount): self
    {
        $this->bankAccounts[] = $bankAccount;
        return $this;
    }

    /** @inheritdoc */
    public function getLicense(): ?string
    {
        return $this->license;
    }

    /** @inheritdoc */
    public function setLicense(string $license): self
    {
        $this->license = $license;
        return $this;
    }

    /** @inheritdoc */
    public function getActionInFavor(): ?string
    {
        return $this->actionInFavor;
    }

    /** @inheritdoc */
    public function setActionInFavor(string $actionInFavor): self
    {
        $this->actionInFavor = $actionInFavor;
        return $this;
    }

    /** @inheritdoc */
    public function getCommission(): ?string
    {
        return $this->commission;
    }

    /** @inheritdoc */
    public function setCommission(string $commission): self
    {
        $this->commission = $commission;
        return $this;
    }

    /** @inheritdoc */
    public function getAdditionalFields(): array
    {
        return $this->additionalFields;
    }

    /** @inheritdoc */
    public function getAdditionalFieldByKey(int $key): ?string
    {
        return $this->additionalFields[$key] ?? null;
    }

    /** @inheritdoc */
    public function setAdditionalFieldByKey(int $key, string $value): self
    {
        $this->additionalFields[$key] = $value;

        return $this;
    }

    /** @inheritdoc */
    public function toArray(): array
    {
        $array = [
            'foreignName' => $this->getForeignName(),
            'phones' => $this->getPhones(),
            'emails' => $this->getEmails(),
            'legalAddress' => $this->getLegalAddress()->toArray(),
            'postAddress' => $this->getPostAddress() ? $this->getPostAddress()->toArray() : null,
            'actualAddress' => $this->getActualAddress()->toArray(),
            'ceo' => $this->getCeo() ? $this->getCeo()->toArray() : null,
            'owners' =>
                !empty($this->getOwners())
                    ? array_map(static fn (QstSchemaOwnerInterface $owner) => $owner->toArray(), $this->getOwners())
                    : null,
            'boardOfDirectors' => $this->getBoardOfDirectors(),
            'managementBoard' => $this->getManagementBoard(),
            'otherManagementBodies' => $this->getOtherManagementBodies(),
            'addressLocation' => $this->getAddressLocation(),
            'birthDate' => $this->getBirthDate(),
            'birthPlace' => $this->getBirthPlace(),
            'identityDoc' => $this->getIdentityDoc() ? $this->getIdentityDoc()->toArray() : null,
            'bankAccounts' => array_map(
                static fn (QstSchemaBankAccountInterface $bankAccount) => ['bankAccount' => $bankAccount->toArray()],
                $this->getBankAccounts()
            ),
            'license' => $this->getLicense(),
            'actionInFavor' => $this->getActionInFavor(),
            'commission' => $this->getCommission(),
        ];

        foreach ($this->additionalFields as $key => $value) {
            $array['additionalField' . $key] = $value;
        }

        return array_filter($array, static fn ($value) => $value !== null);
    }
}
