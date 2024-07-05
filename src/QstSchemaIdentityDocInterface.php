<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaIdentityDocInterface extends QstToArrayInterface
{
    /**
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self;

    /**
     * @return string|null
     */
    public function getSeries(): string;

    /**
     * @param string $series
     * @return $this
     */
    public function setSeries(string $series): self;

    /**
     * @return string|null
     */
    public function getNumber(): string;

    /**
     * @param string $number
     * @return $this
     */
    public function setNumber(string $number): self;

    /**
     * @return string|null
     */
    public function getIssueDate(): string;

    /**
     * @param string $issueDate
     * @return $this
     */
    public function setIssueDate(string $issueDate): self;

    /**
     * @return string|null
     */
    public function getIssuedBy(): string;

    /**
     * @param string $issuedBy
     * @return $this
     */
    public function setIssuedBy(string $issuedBy): self;

    /**
     * @return string|null
     */
    public function getIssuedByKP(): string;

    /**
     * @param string $issuedByKP
     * @return $this
     */
    public function setIssuedByKP(string $issuedByKP): self;
}