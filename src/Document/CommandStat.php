<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class CommandStat
 * @package App\Document
 * @MongoDB\Document(collection="commandstats", repositoryClass="App\Repository\CommandStatRepository")
 */
class CommandStat
{

    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $commandId;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $uses = 0;

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
