<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Документ, удостоверяющей личность, в анкете
 */
class QstSchemaIdentityDoc implements QstSchemaIdentityDocInterface
{
    public const TYPE = [
        'passport' => 'PASSPORT',
        'other' => 'OTHER'
    ];

    private ?string $type = null;
    private string $series;
    private string $number;
    private string $issueDate;
    private string $issuedBy;
    private string $issuedByKP;

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

    /** @inheritdoc */
    public function getSeries(): string
    {
        return $this->series;
    }

    /** @inheritdoc */
    public function setSeries(string $series): self
    {
        $this->series = $series;
        return $this;
    }

    /** @inheritdoc */
    public function getNumber(): string
    {
        return $this->number;
    }

    /** @inheritdoc */
    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    /** @inheritdoc */
    public function getIssueDate(): string
    {
        return $this->issueDate;
    }

    /** @inheritdoc */
    public function setIssueDate(string $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    /** @inheritdoc */
    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    /** @inheritdoc */
    public function setIssuedBy(string $issuedBy): self
    {
        $this->issuedBy = $issuedBy;
        return $this;
    }

    /** @inheritdoc */
    public function getIssuedByKP(): string
    {
        return $this->issuedByKP;
    }

    /** @inheritdoc */
    public function setIssuedByKP(string $issuedByKP): self
    {
        $this->issuedByKP = $issuedByKP;
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): ?array
    {
        $array = [
            'type' => $this->getType(),
            'series' => $this->getSeries(),
            'number' => $this->getNumber(),
            'issueDate' => $this->getIssueDate(),
            'issuedBy' => $this->getIssuedBy(),
            'issuedByKP' => $this->getIssuedByKP()
        ];

        return array_filter($array, static fn ($value) => $value !== null);
    }
}
