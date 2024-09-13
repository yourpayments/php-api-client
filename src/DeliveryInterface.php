<?php declare(strict_types=1);

namespace Ypmn;

interface DeliveryInterface
{
    /**
     * Установить Имя Получателя
     * @param string $firstName Имя Получателя
     * @return $this
     */
    public function setFirstName(string $firstName) : self;

    /**
     * Получить Имя Получателя
     * @return null|string Имя Получателя
     */
    public function getFirstName() : ?string;

    /**
     * Установить Фамилию Получателя
     * @param string $lastName Фамилия Получателя
     * @return $this
     */
    public function setLastName(string $lastName) : self;

    /**
     * Получить Фамилию Получателя
     * @return null|string Фамилия Получателя
     */
    public function getLastName() : ?string;

    /**
     * Установить Email Получателя
     * @param string $email Email Получателя
     * @return $this
     */
    public function setEmail(string $email) : self;

    /**
     * Получить Email Получателя
     * @return null|string Email Получателя
     */
    public function getEmail() : ?string;

    /**
     * Установить Номер телефона Получателя
     * @param string $phone Номер телефона Получателя
     * @return $this
     */
    public function setPhone(string $phone) : self;

    /**
     * Получить Номер телефона Получателя
     * @return null|string Номер телефона Получателя
     */
    public function getPhone() : ?string;

    /**
     * Установить Код Страны Получателя
     * @param string $countryCode Код Страны Получателя
     * @return $this
     */
    public function setCountryCode(string $countryCode) : self;

    /**
     * Получить Код Страны Получателя
     * @return null|string Код Страны Получателя
     */
    public function getCountryCode() : ?string;

    /**
     * Установить Регион Получателя
     * @param string $state Регион Получателя
     * @return $this
     */
    public function setState(string $state) : self;

    /**
     * Получить Регион Получателя
     * @return null|string Регион Получателя
     */
    public function getState() : ?string;

    /**
     * Установить Город Получателя
     * @param string $city Город Получателя
     * @return $this
     */
    public function setCity(string $city) : self;

    /**
     * Получить Город Получателя
     * @return null|string Город Получателя
     */
    public function getCity() : ?string;

    /**
     * Установить Адрес Доставки - Первую Строку
     * @param string $addressLine1 Адрес Доставки - Первая Строка
     * @return $this
     */
    public function setAddressLine1(string $addressLine1) : self;

    /**
     * Получить Адрес Доставки - Первую Строку
     * @return null|string Адрес Доставки - Первая Строка
     */
    public function getAddressLine1() : ?string;

    /**
     * Получить Адрес Доставки - Вторую Строку
     * @param string $addressLine2 Адрес Доставки - Вторая Строка
     * @return $this
     */
    public function setAddressLine2(string $addressLine2) : self;

    /**
     * Установить Адрес Доставки - Вторую Строку
     * @return null|string Адрес Доставки - Вторая Строка
     */
    public function getAddressLine2() : ?string;

    /**
     * Установить Почтовый Индекс Адреса Доставки
     * @param string $zipCode Почтовый Индекс Адреса Доставки
     * @return DeliveryInterface
     */
    public function setZipCode(string $zipCode) : self;

    /**
     * Плучить Почтовый Индекс Адреса Доставки
     * @return null|string Почтовый Индекс Адреса Доставки
     */
    public function getZipCode() : ?string;

    /**
     * Установить Наименование Компании Получателя
     * @param string $companyName Наименование Компании Получателя
     * @return $this
     */
    public function setCompanyName(string $companyName): self;

    /**
     * Плучить Наименование Компании Получателя
     * @return null|string Наименование Компании Получателя
     */
    public function getCompanyName(): ?string;

    /**
     * Установить Удостоверение личности Получателя
     * @param IdentityDocumentInterface $identityDocument Удостоверение личности Получателя
     * @return $this
     */
    public function setIdentityDocument(IdentityDocumentInterface $identityDocument): self;

    /**
     * Получить Удостоверение личности Получателя
     * @return IdentityDocumentInterface|null Удостоверение личности Получателя
     */
    public function getIdentityDocument(): ?IdentityDocumentInterface;
}
