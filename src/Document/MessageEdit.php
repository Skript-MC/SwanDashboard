<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class MessageEdit
 * @package App\Document
 * @MongoDB\Document(collection="messageedits", repositoryClass="App\Repository\MessageEditRepository")
 */
class MessageEdit
{
    /**
     * @MongoDB\Id(type="string")
     */
    protected string $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=Message::class, nullable=true, storeAs="id")
     */
    protected ?Message $message = null;

    /**
     * @MongoDB\ReferenceOne(targetDocument=DiscordUser::class, storeAs="id")
     */
    protected DiscordUser $user;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $newName;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected array $newAliases;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $newContent;

    /**
     * @MongoDB\Field(type="bool", nullable=true)
     */
    protected ?bool $validated = null;

    /**
     * @MongoDB\ReferenceOne(targetDocument=DiscordUser::class, nullable=true, storeAs="id")
     */
    protected ?DiscordUser $reviewer = null;

    /**
     * @MongoDB\Field(type="string")
     */
    protected ?string $messageType = null;

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
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param Message|null $message
     */
    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }

    /**
     * @return DiscordUser
     */
    public function getUser(): DiscordUser
    {
        return $this->user;
    }

    /**
     * @param DiscordUser $user
     */
    public function setUser(DiscordUser $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @param string $newName
     */
    public function setNewName(string $newName): void
    {
        $this->newName = $newName;
    }

    /**
     * @return array
     */
    public function getNewAliases(): array
    {
        return $this->newAliases;
    }

    /**
     * @param array $newAliases
     */
    public function setNewAliases(array $newAliases): void
    {
        $this->newAliases = $newAliases;
    }

    /**
     * @return string
     */
    public function getNewContent(): string
    {
        return $this->newContent;
    }

    /**
     * @param string $newContent
     */
    public function setNewContent(string $newContent): void
    {
        $this->newContent = $newContent;
    }

    /**
     * @return bool|null
     */
    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    /**
     * @param bool|null $validated
     */
    public function setValidated(?bool $validated): void
    {
        $this->validated = $validated;
    }

    /**
     * @return DiscordUser|null
     */
    public function getReviewer(): ?DiscordUser
    {
        return $this->reviewer;
    }

    /**
     * @param DiscordUser|null $reviewer
     */
    public function setReviewer(?DiscordUser $reviewer): void
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @return string|null
     */
    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    /**
     * @param string|null $messageType
     */
    public function setMessageType(?string $messageType): void
    {
        $this->messageType = $messageType;
    }

    /**
     * Used for make a diff with an empty edit and a simple edit.
     * @return $this
     */
    public static function getEmptyEdit(): self
    {
        $emptyEdit = new self();
        $emptyEdit->setNewName('Sans nom');
        $emptyEdit->setNewAliases([]);
        $emptyEdit->setNewContent('');
        $emptyEdit->setMessageType('');
        return $emptyEdit;
    }

}
