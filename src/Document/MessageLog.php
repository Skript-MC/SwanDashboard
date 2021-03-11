<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class MessageLog
 * @package App\Document
 * @MongoDB\Document(collection="messagelogs")
 */
class MessageLog
{
    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=DiscordUser::class, storeAs="id")
     */
    protected DiscordUser $user;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $messageId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $channelId;

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
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
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
