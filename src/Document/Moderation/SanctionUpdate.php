<?php

namespace App\Document\Moderation;

use DateInterval;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Exception;

/**
 * Class SanctionUpdate
 * @package App\Document\Moderation
 * @MongoDB\Document
 * @MongoDB\EmbedOne
 */
class SanctionUpdate
{

    /**
     * @MongoDB\Id
     */
    protected string $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $date;

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
        return date_create()->setTimestamp($this->date / 1000);
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date->getTimestamp();
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
     * @return string
     * @throws Exception
     */
    public function getValueBefore(): string
    {
        $d1 = new DateTime();
        $d2 = new DateTime();
        $d2->add(new DateInterval('PT' . ($this->valueBefore / 1000 ?? 0) . 'S'));
        return $d2->diff($d1)->format('%m mois, %d jour(s), %h heure(s), %i minute(s) et %s seconde(s)');
    }

    /**
     * @param DateTime $date
     */
    public function setValueBefore(DateTime $date): void
    {
        $this->valueBefore = $date->getTimestamp();
    }
    /**
     * @return string
     * @throws Exception
     */
    public function getValueAfter(): string
    {
        $d1 = new DateTime();
        $d2 = new DateTime();
        $d2->add(new DateInterval('PT' . ($this->valueAfter / 1000 ?? 0) . 'S'));
        return $d2->diff($d1)->format('%m mois, %d jour(s), %h heure(s), %i minute(s) et %s seconde(s)');
    }

    /**
     * @param DateTime $date
     */
    public function setValueAfter(DateTime $date): void
    {
        $this->valueAfter = $date->getTimestamp();
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
