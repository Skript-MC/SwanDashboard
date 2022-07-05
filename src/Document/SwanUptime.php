<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class SwanModule
 * @package App\Document
 * @MongoDB\Document(collection="uptimes", repositoryClass="App\Repository\SwanUptimeRepository")
 *
 */
class SwanUptime
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private ?string $day = null;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $hours = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return SwanUptime
     */
    public function setId(string $id): SwanUptime
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDay(): ?string
    {
        return $this->day;
    }

    /**
     * @param string|null $day
     * @return SwanUptime
     */
    public function setDay(?string $day): SwanUptime
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @return array
     */
    public function getHours(): array
    {
        return $this->hours;
    }

    /**
     * @param array $hours
     * @return SwanUptime
     */
    public function setHours(array $hours): SwanUptime
    {
        $this->hours = $hours;
        return $this;
    }
}
