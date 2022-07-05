<?php

namespace App\Document;

use DateTime;
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
    private string $id;

    /**
     * @MongoDB\EmbedOne(targetDocument=Message::class, nullable=true)
     */
    private ?Message $message = null;

    /**
     * @MongoDB\ReferenceOne(targetDocument=DashUser::class, storeAs="id")
     */
    private DashUser $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument=DashUser::class, nullable=true, storeAs="id")
     */
    private ?DashUser $reviewer = null;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $newName;

    /**
     * @MongoDB\Field(type="collection")
     */
    private array $newAliases;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $newContent;

    /**
     * @MongoDB\Field(type="bool", nullable=true)
     */
    private ?bool $validated = null;

    /**
     * @MongoDB\Field(type="string")
     */
    private ?string $messageType = null;

    /**
     * @MongoDB\Field(type="date")
     */
    private DateTime $createdAt;

    /**
     * @MongoDB\Field(type="date", nullable=true)
     */
    private ?DateTime $reviewedAt = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return MessageEdit
     */
    public function setId(string $id): MessageEdit
    {
        $this->id = $id;
        return $this;
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
     * @return MessageEdit
     */
    public function setMessage(?Message $message): MessageEdit
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return DashUser
     */
    public function getUser(): DashUser
    {
        return $this->user;
    }

    /**
     * @param DashUser $user
     * @return MessageEdit
     */
    public function setUser(DashUser $user): MessageEdit
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return DashUser|null
     */
    public function getReviewer(): ?DashUser
    {
        return $this->reviewer;
    }

    /**
     * @param DashUser|null $reviewer
     * @return MessageEdit
     */
    public function setReviewer(?DashUser $reviewer): MessageEdit
    {
        $this->reviewer = $reviewer;
        return $this;
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
     * @return MessageEdit
     */
    public function setNewName(string $newName): MessageEdit
    {
        $this->newName = $newName;
        return $this;
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
     * @return MessageEdit
     */
    public function setNewAliases(array $newAliases): MessageEdit
    {
        $this->newAliases = $newAliases;
        return $this;
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
     * @return MessageEdit
     */
    public function setNewContent(string $newContent): MessageEdit
    {
        $this->newContent = $newContent;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    /**
     * @param bool|null $validated
     * @return MessageEdit
     */
    public function setValidated(?bool $validated): MessageEdit
    {
        $this->validated = $validated;
        return $this;
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
     * @return MessageEdit
     */
    public function setMessageType(?string $messageType): MessageEdit
    {
        $this->messageType = $messageType;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return MessageEdit
     */
    public function setCreatedAt(DateTime $createdAt): MessageEdit
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getReviewedAt(): DateTime
    {
        return $this->reviewedAt;
    }

    /**
     * @param DateTime $reviewedAt
     * @return MessageEdit
     */
    public function setReviewedAt(DateTime $reviewedAt): MessageEdit
    {
        $this->reviewedAt = $reviewedAt;
        return $this;
    }

    public function hasBeenReviewed(): bool
    {
        return $this->reviewedAt !== null;
    }

    public function getDashboardNamespace(): string
    {
        return match ($this->messageType) {
            'quickReply' => 'quickreplies',
            'errorInfo' => 'errorinfos',
            'joke' => 'jokes',
        };
    }

    public function getDisplayName(): string
    {
        return match ($this->messageType) {
            'quickReply' => 'Réponse rapide',
            'errorInfo' => 'Information d\'erreur',
            'joke' => 'Blague',
        };
    }

    public function toMessage(): Message
    {
        return (new Message())
            ->setMessageType($this->messageType)
            ->setName($this->newName)
            ->setAliases($this->newAliases)
            ->setContent($this->newContent);
    }
}
