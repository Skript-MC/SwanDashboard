<?php

namespace App\Document\Moderation;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class SanctionUpdate
 * @package App\Document\Moderation
 * @MongoDB\EmbedOne
 */
class SanctionUpdate
{
    /**
     * @MongoDB\Field(type="date")
     */
    protected DateTime $date;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $moderator;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $type;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    protected ?int $valueBefore = null;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    protected ?int $valueAfter = null;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $reason;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getModerator(): string
    {
        return $this->moderator;
    }

    /**
     * @param string $moderator
     */
    public function setModerator(string $moderator): void
    {
        $this->moderator = $moderator;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getValueBefore(): ?int
    {
        return $this->valueBefore;
    }

    /**
     * @param int|null $valueBefore
     */
    public function setValueBefore(?int $valueBefore): void
    {
        $this->valueBefore = $valueBefore;
    }

    /**
     * @return int|null
     */
    public function getValueAfter(): ?int
    {
        return $this->valueAfter;
    }

    /**
     * @param int|null $valueAfter
     */
    public function setValueAfter(?int $valueAfter): void
    {
        $this->valueAfter = $valueAfter;
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

}
