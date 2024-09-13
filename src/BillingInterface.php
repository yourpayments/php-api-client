<?php declare(strict_types=1);

namespace Ypmn;

interface BillingInterface
{
    /**
     * Установить Имя
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName) : self;

    /**
     * Получить Имя
     * @return string
     */
    public function getFirstName() : string;

    /**
     * Установить Фамилия
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName) : self;

    /**
     * Получить Фамилия
     * @return string
     */
    public function getLastName() : string;

    /**
     * Установить Email
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email) : self;

    /**
     * Получить Email
     * @return string
     */
    public function getEmail() : string;

    /**
     * Установить Номер Телефона
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone) : self;

    /**
     * Получить Номер Телефона
     * @return string
     */
    public function getPhone() : ?string;

    /**
     * Установить Код Страны
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode(string $countryCode) : self;

    /**
     * Получить Код Страны
     * @return string
     */
    public function getCountryCode() : ?string;

    /**
     * Установить Город
     * @param string $city
     * @return $this
     */
    public function setCity(string $city) : self;

    /**
     * Получить Город
     * @return string
     */
    public function getCity() : ?string;

    /**
     * Установить Регион
     * @param string $state
     * @return $this
     */
    public function setState(string $state) : self;

    /**
     * Получить Регион
     * @return string
     */
    public function getState() : ?string;

    /**
     * Установить Название Компании
     * @param string $companyName
     * @return $this
     */
    public function setCompanyName(string $companyName) : self;

    /**
     * Получить Название Компании
     * @return null|string
     */
    public function getCompanyName() : ?string;

    /**
     * Установить ИНН
     * @param string $taxId
     * @return $this
     */
    public function setTaxId(string $taxId) : self;

    /**
     * Получить ИНН
     * @return null|string
     */
    public function getTaxId() : ?string;

    /**
     * Установить Адрес - строка 1
     * @param string $addressLine1
     * @return $this
     */
    public function setAddressLine1(string $addressLine1) : self;

    /**
     * Получить Адрес - строка 1
     * @return null|string
     */
    public function getAddressLine1() : ?string;

    /**
     * Установить Адрес - строка 2
     * @param string $addressLine2
     * @return $this
     */
    public function setAddressLine2(string $addressLine2) : self;

    /**
     * Получить Адрес - строка 2
     * @return null|string
     */
    public function getAddressLine2() : ?string;

    /**
     * Установить Почтовый Индекс
     * @param string $zipCode
     * @return $this
     */
    public function setZipCode(string $zipCode) : self;

    /**
     * Получить Почтовый Индекс
     * @return null|string
     */
    public function getZipCode() : ?string;

    /**
     * Установить Удостоверение Личности
     * @param IdentityDocumentInterface $identityDocument Удостоверение Личности
     * @return $this
     */
    public function setIdentityDocument(IdentityDocumentInterface $identityDocument): self;

    /**
     * Получить Удостоверение Личности
     * @return null|IdentityDocumentInterface Удостоверение Личности
     */
    public function getIdentityDocument(): ?IdentityDocumentInterface;


    /**
     * Получить тип биллинга
     * @return string
     */
    public function getType(): string;

    /**
     * Установить тип биллинга
     * @param string $type
     * @return Billing
     */
    public function setType(string $type): self;

}
