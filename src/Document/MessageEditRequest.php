<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="messageEditRequests")
 */
class MessageEditRequest
{
    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=Message::class, nullable=true, storeAs="id")
     */
    protected ?Message $message = null;

    /**
     * @MongoDB\ReferenceOne(targetDocument=User::class, storeAs="id")
     */
    protected User $user;

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
     * @MongoDB\ReferenceOne(targetDocument=User::class, nullable=true, storeAs="id")
     */
    protected ?User $reviewer = null;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
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
     * @param bool $validated
     */
    public function setValidated(bool $validated): void
    {
        $this->validated = $validated;
    }

    /**
     * @return User|null
     */
    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    /**
     * @param User|null $reviewer
     */
    public function setReviewer(?User $reviewer): void
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

}
