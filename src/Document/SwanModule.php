<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class SwanModule
 * @package App\Document
 * @MongoDB\Document(collection="swanmodules", repositoryClass="App\Repository\SwanModuleRepository")
 */
class SwanModule
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $name;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $description = "Aucune description n'est disponible pour ce module.";

    /**
     * @MongoDB\Field(type="string")
     */
    private string $store;

    /**
     * @MongoDB\Field(type="bool")
     */
    private bool $enabled;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return SwanModule
     */
    public function setId(string $id): SwanModule
    {
        $this->id = $id;
        return $this;
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
     * @return SwanModule
     */
    public function setName(string $name): SwanModule
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return SwanModule
     */
    public function setDescription(string $description): SwanModule
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @param string $store
     * @return SwanModule
     */
    public function setStore(string $store): SwanModule
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return SwanModule
     */
    public function setEnabled(bool $enabled): SwanModule
    {
        $this->enabled = $enabled;
        return $this;
    }
}
