<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class SwanChannel
 * @package App\Document
 * @MongoDB\Document(collection="swanchannels", repositoryClass="App\Repository\SwanChannelRepository")
 */
class SwanChannel
{
    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $channelId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $name;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected bool $logged;

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
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId): void
    {
        $this->channelId = $channelId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return $this->logged;
    }

    /**
     * @param bool $logged
     */
    public function setLogged(bool $logged): void
    {
        $this->logged = $logged;
    }

}
