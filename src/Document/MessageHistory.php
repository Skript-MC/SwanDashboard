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
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument=User::class)
     */
    protected User $user;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $messageId;

    /**
     * @MongoDB\ReferenceOne(targetDocument=Channel::class)
     */
    protected Channel $channel;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
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
     * @return Channel
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     */
    public function setChannel(Channel $channel): void
    {
        $this->channel = $channel;
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
     * @return string
     */
    public function getNewContent(): ?string
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

}
