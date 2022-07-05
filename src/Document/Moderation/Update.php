<?php

namespace App\Document\Moderation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Update
 * @package App\Document\Moderation
 * @MongoDB\EmbeddedDocument()
 */
class Update
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $date;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $moderator;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $type;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    private ?int $valueBefore = null;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    private ?int $valueAfter = null;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $reason;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Update
     */
    public function setId(string $id): Update
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @param int $date
     * @return Update
     */
    public function setDate(int $date): Update
    {
        $this->date = $date;
        return $this;
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
     * @return Update
     */
    public function setModerator(string $moderator): Update
    {
        $this->moderator = $moderator;
        return $this;
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
     * @return Update
     */
    public function setType(string $type): Update
    {
        $this->type = $type;
        return $this;
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
     * @return Update
     */
    public function setValueBefore(?int $valueBefore): Update
    {
        $this->valueBefore = $valueBefore;
        return $this;
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
     * @return Update
     */
    public function setValueAfter(?int $valueAfter): Update
    {
        $this->valueAfter = $valueAfter;
        return $this;
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
     * @return Update
     */
    public function setReason(string $reason): Update
    {
        $this->reason = $reason;
        return $this;
    }
}
