<?php

declare(strict_types=1);

namespace Ypmn;

interface QstSchemaOwnerInterface extends QstToArrayInterface
{
    /**
     * @return string
     */
    public function getOwner(): string;

    /**
     * @param string $owner
     * @return $this
     */
    public function setOwner(string $owner): self;

    /**
     * @return string
     */
    public function getShare(): string;

    /**
     * @param string $share
     * @return $this
     */
    public function setShare(string $share): self;
}