<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaIdentityDocInterface extends QstToArrayInterface
{
    /**
     * Тип документа:
     *  - PASSPORT - паспорт РФ;
     *  - OTHER - иной документ, удостоверяющий личность
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Тип документа:
     * - PASSPORT - паспорт РФ;
     * - OTHER - иной документ, удостоверяющий личность
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self;

    /**
     * Серия документа
     * @return string|null
     */
    public function getSeries(): string;

    /**
     * Серия документа
     * @param string $series
     * @return $this
     */
    public function setSeries(string $series): self;

    /**
     * Номер документа
     * @return string|null
     */
    public function getNumber(): string;

    /**
     * Номер документа
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number): self;

    /**
     * Дата выдачи документа
     * @return string|null
     */
    public function getIssueDate(): string;

    /**
     * Дата выдачи документа
     * @param string $issueDate
     * @return $this
     */
    public function setIssueDate(string $issueDate): self;

    /**
     * Кем выдан документ
     * @return string|null
     */
    public function getIssuedBy(): string;

    /**
     * Кем выдан документ
     * @param string $issuedBy
     * @return $this
     */
    public function setIssuedBy(string $issuedBy): self;

    /**
     * Кем выдан документ (к/п)
     * @return string|null
     */
    public function getIssuedByKP(): string;

    /**
     * Кем выдан документ (к/п)
     * @param string $issuedByKP
     * @return $this
     */
    public function setIssuedByKP(string $issuedByKP): self;
}