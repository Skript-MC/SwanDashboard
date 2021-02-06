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
    private string $lastBanId;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $lastMuteId;

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
     * @return string
     */
    public function getLastBanId(): string
    {
        return $this->lastBanId;
    }

    /**
     * @param string $lastBanId
     */
    public function setLastBanId(string $lastBanId): void
    {
        $this->lastBanId = $lastBanId;
    }

    /**
     * @return string
     */
    public function getLastMuteId(): string
    {
        return $this->lastMuteId;
    }

    /**
     * @param string $lastMuteId
     */
    public function setLastMuteId(string $lastMuteId): void
    {
        $this->lastMuteId = $lastMuteId;
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
