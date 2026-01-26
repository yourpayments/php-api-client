<?php

declare(strict_types=1);

namespace Ypmn;

use DateTimeZone;

class Receiver implements \JsonSerializable
{
    private string $sku;
    private string $bic;
    private string $correspondentAccount;
    private string $account;
    private string $name;
    private string $inn;
    private string $kpp;
    private string $purpose;
    private string $personalAccount;
    private string $period;

    public function __construct(
        string $sku,
        string $bic,
        string $correspondentAccount,
        string $account,
        string $name,
        string $inn,
        string $kpp,
        string $purpose,
        string $personalAccount,
        string $period
    ) {
        $this->sku = $sku;
        $this->bic = $bic;
        $this->correspondentAccount = $correspondentAccount;
        $this->account = $account;
        $this->name = $name;
        $this->inn = $inn;
        $this->kpp = $kpp;
        $this->purpose = $purpose;
        $this->personalAccount = $personalAccount;

        if (preg_match('~^(0[1-9]|1[0-2])\/\d{4}$~', $period)) {
            throw new PaymentException('Аргумент period имеет неверный формат');
        }

        $this->period = $period;
    }

    public function toArray(): array
    {
        return [
            'sku' => $this->sku,
            'bic' => $this->bic,
            'correspondentAccount' => $this->correspondentAccount,
            'account' => $this->account,
            'name' => $this->name,
            'inn' => $this->inn,
            'kpp' => $this->kpp,
            'purpose' => $this->purpose,
            'personalAccount' => $this->personalAccount,
            'period' => $this->period
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
