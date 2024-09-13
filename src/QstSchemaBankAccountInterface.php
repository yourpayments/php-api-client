<?php declare(strict_types=1);

namespace Ypmn;

interface QstSchemaBankAccountInterface extends QstToArrayInterface
{
    /**
     * БИК
     * @return string
     */
    public function getBankBIK(): string;

    /**
     * БИК
     * @param string $bankBIK
     * @return $this
     */
    public function setBankBIK(string $bankBIK): self;

    /**
     * Кор. счет
     * @return string
     */
    public function getBankCorAccount(): string;

    /**
     * Кор. счет
     * @param string $bankCorAccount
     * @return $this
     */
    public function setBankCorAccount(string $bankCorAccount): self;

    /**
     * Номер счета
     * @return string
     */
    public function getBankAccount(): string;

    /**
     * Номер счета
     * @param string $bankAccount
     * @return $this
     */
    public function setBankAccount(string $bankAccount): self;
}
