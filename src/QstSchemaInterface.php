<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaInterface extends QstToArrayInterface
{
    /**
     * @return string|null
     */
    public function getForeignName(): ?string;

    /**
     * @param string $foreignName
     * @return $this
     */
    public function setForeignName(string $foreignName): self;

    /**
     * @return array
     */
    public function getPhones(): array;

    /**
     * @param string $phone
     * @return $this
     */
    public function addPhone(string $phone): self;

    /**
     * @return array
     */
    public function getEmails(): array;

    /**
     * @param string $email
     * @return $this
     */
    public function addEmail(string $email): self;

    /**
     * @return QstSchemaAddressInterface
     */
    public function getLegalAddress(): QstSchemaAddressInterface;

    /**
     * @param QstSchemaAddressInterface $legalAddress
     * @return $this
     */
    public function setLegalAddress(QstSchemaAddressInterface $legalAddress): self;

    /**
     * @return QstSchemaAddressInterface|null
     */
    public function getPostAddress(): ?QstSchemaAddressInterface;

    /**
     * @param QstSchemaAddressInterface $postAddress
     * @return $this
     */
    public function setPostAddress(QstSchemaAddressInterface $postAddress): self;

    /**
     * @return QstSchemaAddressInterface
     */
    public function getActualAddress(): QstSchemaAddressInterface;

    /**
     * @param QstSchemaAddressInterface $actualAddress
     * @return $this
     */
    public function setActualAddress(QstSchemaAddressInterface $actualAddress): self;

    /**
     * @return QstSchemaCeoInterface|null
     */
    public function getCeo(): ?QstSchemaCeoInterface;

    /**
     * @param QstSchemaCeoInterface $ceo
     * @return $this
     */
    public function setCeo(QstSchemaCeoInterface $ceo): self;

    /**
     * @return QstSchemaOwnerInterface[]
     */
    public function getOwners(): array;

    /**
     * @param QstSchemaOwnerInterface $owner
     * @return $this
     */
    public function addOwner(QstSchemaOwnerInterface $owner): self;

    /**
     * Совет директоров / наблюдательный совет (состав)
     * @return string|null
     */
    public function getBoardOfDirectors(): ?string;

    /**
     * Совет директоров / наблюдательный совет (состав)
     * @param string $boardOfDirectors
     * @return $this
     */
    public function setBoardOfDirectors(string $boardOfDirectors): self;

    /**
     * Правление (дирекция)
     * @return string|null
     */
    public function getManagementBoard(): ?string;

    /**
     * Правление (дирекция)
     * @param string $managementBoard
     * @return $this
     */
    public function setManagementBoard(string $managementBoard): self;

    /**
     * Другие органы управления
     * @return string|null
     */
    public function getOtherManagementBodies(): ?string;

    /**
     * Другие органы управления
     * @param string $otherManagementBodies
     * @return $this
     */
    public function setOtherManagementBodies(string $otherManagementBodies): self;

    /**
     * Присутствует ли по месту нахождения юридического лица постоянно действующий исполнительный орган
     * @return string|null
     */
    public function getAddressLocation(): ?string;

    /**
     * Присутствует ли по месту нахождения юридического лица постоянно действующий исполнительный орган
     * @param string $addressLocation
     * @return $this
     */
    public function setAddressLocation(string $addressLocation): self;

    /**
     * Дата рождения ИП
     * @return string
     */
    public function getBirthDate(): ?string;

    /**
     * Дата рождения ИП
     * @param string $birthDate
     * @return $this
     */
    public function setBirthDate(string $birthDate): self;

    /**
     * Место рождения ИП
     * @return string
     */
    public function getBirthPlace(): ?string;

    /**
     * Место рождения ИП
     * @param string $birthPlace
     * @return $this
     */
    public function setBirthPlace(string $birthPlace): self;

    /**
     * Документ удостоверяющий личность ИП
     * @return QstSchemaIdentityDocInterface
     */
    public function getIdentityDoc(): ?QstSchemaIdentityDocInterface;

    /**
     * Документ удостоверяющий личность ИП
     * @param QstSchemaIdentityDocInterface $identityDoc
     * @return $this
     */
    public function setIdentityDoc(QstSchemaIdentityDocInterface $identityDoc): self;

    /**
     * Список банковских реквизитов
     * @return QstSchemaBankAccountInterface[]
     */
    public function getBankAccounts(): array;

    /**
     * Список банковских реквизитов
     * @param QstSchemaBankAccountInterface $bankAccount
     * @return $this
     */
    public function addBankAccount(QstSchemaBankAccountInterface $bankAccount): self;

    /**
     * Сведения о лицензии на право осуществления деятельности, подлежащей лицензированию:
     * вид, номер, дата выдачи лицензии; кем выдана; срок действия; перечень видов лицензируемой деятельности
     * @return string|null
     */
    public function getLicense(): ?string;

    /**
     * Сведения о лицензии на право осуществления деятельности, подлежащей лицензированию:
     * вид, номер, дата выдачи лицензии; кем выдана; срок действия; перечень видов лицензируемой деятельности
     * @param string $license
     * @return $this
     */
    public function setLicense(string $license): self;

    /**
     * Сведения об основаниях, свидетельствующих о том,
     * что Вы действуете к выгоде другого лица при проведении операций и иных сделок
     * @return string|null
     */
    public function getActionInFavor(): ?string;

    /**
     * Сведения об основаниях, свидетельствующих о том,
     * что Вы действуете к выгоде другого лица при проведении операций и иных сделок
     * @param string $actionInFavor
     * @return $this
     */
    public function setActionInFavor(string $actionInFavor): self;

    /**
     * Вознаграждение Оператора из суммы денежных средств,
     * подлежащих переводу в адрес выгодоприобретателя, Оператором не удерживается
     * @return string|null
     */
    public function getCommission(): ?string;

    /**
     * Вознаграждение Оператора из суммы денежных средств,
     * подлежащих переводу в адрес выгодоприобретателя, Оператором не удерживается
     * @param string $commission
     * @return $this
     */
    public function setCommission(string $commission): self;

    /**
     * Доп. поля в анкете
     * @return array
     */
    public function getAdditionalFields(): array;

    /**
     * Доп. поля в анкете
     * @param int $key
     * @return string|null
     */
    public function getAdditionalFieldByKey(int $key): ?string;

    /**
     * Доп. поля в анкете
     * @param int $key
     * @param string $value
     * @return $this
     */
    public function setAdditionalFieldByKey(int $key, string $value): self;
}
