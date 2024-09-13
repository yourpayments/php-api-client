<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Документ, подтверждающий личность
 */
class IdentityDocument implements IdentityDocumentInterface
{
    /** @var int Номер документа */
    private int $number;

    /** @var string Вид документа */
    private string $type;

    /** @inheritDoc */
    public function __construct(int $number, string $type) {
        $this
            ->setNumber($number)
            ->setType($type);
    }

    /** @inheritDoc */
    public function getNumber(): int
    {
        return $this->number;
    }

    /** @inheritDoc */
    public function setNumber(int $number): self
    {
        $this->number = $number;
        return $this;
    }

    /** @inheritDoc */
    public function getType(): string
    {
        return $this->type;
    }

    /** @inheritDoc */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /** @inheritDoc */
    public function arraySerialize(): array
    {
        return [
            'number' => $this->getNumber(),
            'type' => $this->getType(),
        ];
    }
}
