<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaOwnerInterface extends QstToArrayInterface
{
    /**
     * Имя собственника
     * @return string
     */
    public function getOwner(): string;

    /**
     * Имя собственника
     * @param string $owner
     * @return $this
     */
    public function setOwner(string $owner): self;

    /**
     * Доля собственника
     * @return string
     */
    public function getShare(): string;

    /**
     * Доля собственника
     * @param string $share
     * @return $this
     */
    public function setShare(string $share): self;
}