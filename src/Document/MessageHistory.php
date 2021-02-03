<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="messageHistories")
 */
class MessageHistory
{
    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=User::class)
     */
    protected User $user;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $messageId;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $channelId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $oldContent;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected array $editions;

    /**
     * @MongoDB\Field(type="string", nullable=true)
     */
    protected ?string $newContent = null;

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
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /**
     * @param int $messageId
     */
    public function setMessageId(int $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return int
     */
    public function getChannelId(): int
    {
        return $this->channelId;
    }

    /**
     * @param int $channelId
     */
    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    /**
     * @return string
     */
    public function getOldContent(): string
    {
        return $this->oldContent;
    }

    /**
     * @param string $oldContent
     */
    public function setOldContent(string $oldContent): void
    {
        $this->oldContent = $oldContent;
    }

    /**
     * @return array
     */
    public function getEditions(): array
    {
        return $this->editions;
    }

    /**
     * @param array $editions
     */
    public function setEditions(array $editions): void
    {
        $this->editions = $editions;
    }

    /**
     * @return string|null
     */
    public function getNewContent(): ?string
    {
        return $this->newContent;
    }

    /**
     * @param string|null $newContent
     */
    public function setNewContent(?string $newContent): void
    {
        $this->newContent = $newContent;
    }

}
