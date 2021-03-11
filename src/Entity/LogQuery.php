<?php

namespace App\Entity;

/**
 * Class LogQuery
 * @package App\Entity
 */
class LogQuery
{

    private ?string $userId = null;

    private ?int $beforeDate = null;

    private ?int $afterDate = null;

    private ?int $messageId = null;

    private ?string $oldContent = null;

    private ?string $newContent = null;

    private ?int $channelId = null;

    /**
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @param string|null $userId
     */
    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getBeforeDate(): ?int
    {
        return $this->beforeDate;
    }

    /**
     * @param int|null $beforeDate
     */
    public function setBeforeDate(?int $beforeDate): void
    {
        $this->beforeDate = $beforeDate;
    }

    /**
     * @return int|null
     */
    public function getAfterDate(): ?int
    {
        return $this->afterDate;
    }

    /**
     * @param int|null $afterDate
     */
    public function setAfterDate(?int $afterDate): void
    {
        $this->afterDate = $afterDate;
    }

    /**
     * @return int|null
     */
    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    /**
     * @param int|null $messageId
     */
    public function setMessageId(?int $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return string|null
     */
    public function getOldContent(): ?string
    {
        return $this->oldContent;
    }

    /**
     * @param string|null $oldContent
     */
    public function setOldContent(?string $oldContent): void
    {
        $this->oldContent = $oldContent;
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

    /**
     * @return int|null
     */
    public function getChannelId(): ?int
    {
        return $this->channelId;
    }

    /**
     * @param int|null $channelId
     */
    public function setChannelId(?int $channelId): void
    {
        $this->channelId = $channelId;
    }

}
