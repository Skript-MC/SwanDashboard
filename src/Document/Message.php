<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Message
 * @package App\Document
 * @MongoDB\Document(collection="messages", repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $messageType;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $name;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $aliases;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $content;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Message
     */
    public function setId(string $id): Message
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     * @return Message
     */
    public function setMessageType(string $messageType): Message
    {
        $this->messageType = $messageType;
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
     * @return Message
     */
    public function setName(string $name): Message
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param array $aliases
     * @return Message
     */
    public function setAliases(array $aliases): Message
    {
        $this->aliases = $aliases;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Message
     */
    public function setContent(string $content): Message
    {
        $this->content = $content;
        return $this;
    }

    public function getDisplayName(): string
    {
        return match ($this->messageType) {
            'quickReply' => 'Réponse rapide',
            'errorInfo' => 'Information d\'erreur',
            'joke' => 'Blague',
        };
    }
}
