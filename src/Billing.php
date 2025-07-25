<?php declare(strict_types=1);

namespace Ypmn;

class Billing implements BillingInterface
{
    /** @var string Тип */
    private string $type = 'individual';

    /** @var string Имя */
    private string $firstName;

    /** @var ?string Фамилия */
    private string $lastName;

    /** @var ?string Email */
    private ?string $email = null;

    /** @var ?string Номер телефона */
    private ?string $phone = null;

    /** @var ?string Код Страны */
    private ?string $countryCode = null;

    /** @var ?string Город */
    private ?string $city = null;

    /** @var ?string Регион */
    private ?string $state = null;

    /** @var ?string Название Компании */
    private ?string $companyName = null;

    /** @var ?string Налоговый Идентификатор */
    private ?string $taxId = null;

    /** @var ?string Первая строка адреса */
    private ?string $addressLine1 = null;

    /** @var ?string Вторая строка адреса */
    private ?string $addressLine2 = null;

    /** @var ?string Почтовый индекс */
    private ?string $zipCode = null;

    /** @var ?IdentityDocumentInterface удостоверение личности */
    private ?IdentityDocumentInterface $identityDocument = null;

    /** @inheritDoc */
    public function getFirstName(): ?string
    {
        return $this->firstName ?? null;
    }

    /** @inheritDoc */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /** @inheritDoc */
    public function getLastName(): ?string
    {
        return $this->lastName ?? null;
    }

    /** @inheritDoc */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /** @inheritDoc */
    public function getEmail(): ?string
    {
        return $this->email ?? null;
    }

    /** @inheritDoc */
    public function setEmail(string $email): self
    {

        $this->email = $email;
        return $this;
    }

    /** @inheritDoc */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /** @inheritDoc */
    public function getPhone(): ?string
    {
        return $this->phone ?? null;
    }

    /** @inheritDoc */
    public function getCountryCode(): ?string
    {
        return $this->countryCode ?? null;
    }

    /** @inheritDoc */
    public function setCountryCode(string $countryCode): self
    {
        //TODO: валидация
        $this->countryCode = $countryCode;
        return $this;
    }

    /** @inheritDoc */
    public function getState(): ?string
    {
        return $this->state ?? null;
    }

    /** @inheritDoc */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /** @inheritDoc */
    public function getCompanyName(): ?string
    {
        return $this->companyName ?? null;
    }

    /** @inheritDoc */
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;
        return $this;
    }

    /** @inheritDoc */
    public function getTaxId(): ?string
    {
        return $this->taxId ?? null;
    }

    /** @inheritDoc */
    public function setTaxId(string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    /** @inheritDoc */
    public function getAddressLine1(): ?string
    {
        return $this->addressLine1 ?? null;
    }

    /** @inheritDoc */
    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    /** @inheritDoc */
    public function getAddressLine2(): ?string
    {
        return $this->addressLine2 ?? null;
    }

    /** @inheritDoc */
    public function setAddressLine2(string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;
        return $this;
    }

    /** @inheritDoc */
    public function getZipCode(): ?string
    {
        return $this->zipCode ?? null;
    }

    /** @inheritDoc */
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /** @inheritDoc */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /** @inheritDoc */
    public function getCity(): ?string
    {
        return $this->city ?? null;
    }

    /** @inheritDoc */
    public function setIdentityDocument(IdentityDocumentInterface $identityDocument): self
    {
        $this->identityDocument = $identityDocument;
        return $this;
    }

    /** @inheritDoc */
    public function getIdentityDocument(): ?IdentityDocumentInterface
    {
        return $this->identityDocument ?? null;
    }

    /** @inheritdoc */
    public function getType(): ?string
    {
        return $this->type;
    }

    /** @inheritdoc */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
