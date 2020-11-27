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
    protected $user;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $messageId;

    /**
     * @MongoDB\ReferenceOne(targetDocument=Channel::class)
     */
    protected $channel;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $oldContent;

    /**
     * @MongoDB\Field(type="string", nullable=true)
     */
    protected $newContent;

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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param mixed $messageId
     */
    public function setMessageId($messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getOldContent()
    {
        return $this->oldContent;
    }

    /**
     * @param mixed $oldContent
     */
    public function setOldContent($oldContent): void
    {
        $this->oldContent = $oldContent;
    }

    /**
     * @return mixed
     */
    public function getNewContent()
    {
        return $this->newContent;
    }

    /**
     * @param mixed $newContent
     */
    public function setNewContent($newContent): void
    {
        $this->newContent = $newContent;
    }

}
