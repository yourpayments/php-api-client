<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

class Client implements ClientInterface
{
    /** @var BillingInterface Биллинговая информация */
    private BillingInterface $billing;

    /** @var ?string Клиенский IP */
    private ?string $clientIp = null;

    /** @var string Время оплаты */
    private string $clientTime;

    /** @var string Язык общения с клиентом */
    private string $communicationLanguage = 'RU';

    /** @var DeliveryInterface Информация о доставке */
    private DeliveryInterface $delivery;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    function __construct() {
        $this->setClientIp(Std::get_client_ip());
    }

    /** @inheritDoc */
    public function getBilling(): BillingInterface
    {
        return $this->billing;
    }

    /** @inheritDoc */
    public function setBilling(BillingInterface $billing): self
    {
        $this->billing = $billing;
        return $this;
    }

    /** @inheritDoc */
    public function setCurrentClientIp(): self
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $this->setClientIp($_SERVER['HTTP_CLIENT_IP']);
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->setClientIp($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $this->setClientIp($_SERVER['REMOTE_ADDR']);
        }

        return $this;
    }

    /** @inheritDoc */
    public function getClientIp(): ?string
    {
        return $this->clientIp ?? null;
    }

    /** @inheritDoc */
    public function setClientIp(string $clientIp): self
    {
        $this->clientIp = $clientIp;
        return $this;
    }

    /** @inheritDoc */
    public function setCurrentClientTime(): self
    {
        $this->clientTime = date_create()->format('c');
        return $this;
    }

    /** @inheritDoc */
    public function getClientTime(): ?string
    {
        return $this->clientTime ?? null;
    }

    /** @inheritDoc */
    public function setClientTime(string $clientTime): self
    {
        $this->clientTime = $clientTime;
        return $this;
    }

    /** @inheritDoc */
    public function setDelivery(DeliveryInterface $delivery): self
    {
        $this->delivery = $delivery;
        return $this;
    }

    /** @inheritDoc */
    public function getDelivery(): ?DeliveryInterface
    {
        return $this->delivery ?? null;
    }

    /** @inheritDoc */
    public function getCommunicationLanguage(): ?string
    {
        return $this->communicationLanguage ?? null;
    }

    /** @inheritDoc */
    public function setCommunicationLanguage(string $communicationLanguage): self
    {
        $this->communicationLanguage = $communicationLanguage;
        return $this;
    }

    /** @inheritDoc */
    public function arraySerialize()
    {
        $responseArray = [
            'billing'	=> [
                'firstName'	=> $this->getBilling()->getFirstName(),
                'lastName'	=> $this->getBilling()->getLastName(),
                'email'		=> $this->getBilling()->getEmail(),
                'phone'		=> $this->getBilling()->getPhone(),
                'countryCode'	=> $this->getBilling()->getCountryCode(),
                'city'		=> $this->getBilling()->getCity(),
                'zipCode'		=> $this->getBilling()->getZipCode(),
                'companyName'		=> $this->getBilling()->getCompanyName(),
                'taxId'		=> $this->getBilling()->getTaxId(),
                'addressLine1'		=> $this->getBilling()->getAddressLine1(),
                'addressLine2'		=> $this->getBilling()->getAddressLine2(),
            ],
            'ClientIp'		=> $this->getClientIp(),
            'ClientTime'	=> $this->getClientTime(),
            'communicationLanguage'	=> $this->getCommunicationLanguage(),
        ];

        if (null !== $this->getDelivery()) {
            $responseArray['delivery']	= [
                'firstName'	=> $this->getDelivery()->getFirstName(),
                'lastName'	=> $this->getDelivery()->getLastName(),
                'email'		=> $this->getDelivery()->getEmail(),
                'phone'		=> $this->getDelivery()->getPhone(),
                'countryCode'	=> $this->getDelivery()->getCountryCode(),
                'city'		=> $this->getDelivery()->getCity(),
                'zipCode'		=> $this->getDelivery()->getZipCode(),
                'companyName'		=> $this->getDelivery()->getCompanyName(),
                'addressLine1'		=> $this->getDelivery()->getAddressLine1(),
                'addressLine2'		=> $this->getDelivery()->getAddressLine2(),
            ];
        }

        return $responseArray;
    }
}
