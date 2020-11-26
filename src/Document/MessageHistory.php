<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class MessageHistory
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $userId;

    /**
     * Field not saved.
     */
    protected $userName;

    /**
     * Field not saved.
     */
    protected $userAvatarUrl;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $messageId;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $channelId;

    /**
     * Field not saved.
     */
    protected $channelName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $content;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserAvatarUrl()
    {
        return $this->userAvatarUrl;
    }

    /**
     * @param mixed $userAvatarUrl
     */
    public function setUserAvatarUrl($userAvatarUrl): void
    {
        $this->userAvatarUrl = $userAvatarUrl;
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
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @param mixed $channelId
     */
    public function setChannelId($channelId): void
    {
        $this->channelId = $channelId;
    }

    /**
     * @return mixed
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param mixed $channelName
     */
    public function setChannelName($channelName): void
    {
        $this->channelName = $channelName;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

}
