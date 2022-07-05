<?php

namespace App\Document\Moderation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class ConvictedUser
 * @package App\Document
 * @MongoDB\Document(collection="convictedusers")
 */
class ConvictedUser
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $memberId;

    /**
     * @MongoDB\Field(type="string")
     */
    private ?string $currentBanId = null;

    /**
     * @MongoDB\Field(type="string")
     */
    private ?string $currentMuteId = null;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $currentWarnCount = 0;

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
     * @return string
     */
    public function getMemberId(): string
    {
        return $this->memberId;
    }

    /**
     * @param string $memberId
     */
    public function setMemberId(string $memberId): void
    {
        $this->memberId = $memberId;
    }

    /**
     * @return string|null
     */
    public function getCurrentBanId(): ?string
    {
        return $this->currentBanId;
    }

    /**
     * @param string|null $currentBanId
     */
    public function setCurrentBanId(?string $currentBanId): void
    {
        $this->currentBanId = $currentBanId;
    }

    /**
     * @return string|null
     */
    public function getCurrentMuteId(): ?string
    {
        return $this->currentMuteId;
    }

    /**
     * @param string|null $currentMuteId
     */
    public function setCurrentMuteId(?string $currentMuteId): void
    {
        $this->currentMuteId = $currentMuteId;
    }

    /**
     * @return int
     */
    public function getCurrentWarnCount(): int
    {
        return $this->currentWarnCount;
    }

    /**
     * @param int $currentWarnCount
     */
    public function setCurrentWarnCount(int $currentWarnCount): void
    {
        $this->currentWarnCount = $currentWarnCount;
    }
}
