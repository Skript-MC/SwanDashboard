<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class CommandStat
 * @package App\Document
 * @MongoDB\Document(collection="commandstats")
 * @codeCoverageIgnore Documents and entities should not be unit tested.
 */
class CommandStat
{

    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $commandId;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $uses = 0;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCommandId(): string
    {
        return $this->commandId;
    }

    /**
     * @param string $commandId
     */
    public function setCommandId(string $commandId): void
    {
        $this->commandId = $commandId;
    }

    /**
     * @return int
     */
    public function getUses(): int
    {
        return $this->uses;
    }

    /**
     * @param int $uses
     */
    public function setUses(int $uses): void
    {
        $this->uses = $uses;
    }

}
