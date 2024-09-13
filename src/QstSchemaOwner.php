<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Собственник в анкете
 */
class QstSchemaOwner implements QstSchemaOwnerInterface
{
    private string $owner;
    private string $share;

    /** @inheritdoc */
    public function getOwner(): string
    {
        return $this->owner;
    }

    /** @inheritdoc */
    public function setOwner(string $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /** @inheritdoc */
    public function getShare(): string
    {
        return $this->share;
    }

    /** @inheritdoc */
    public function setShare(string $share): self
    {
        $this->share = $share;
        return $this;
    }

    /** @inheritdoc */
    public function toArray(): array
    {
        return [
            'owner' => $this->getOwner(),
            'share' => $this->getShare(),
        ];
    }
}
