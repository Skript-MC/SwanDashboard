<?php

namespace App\Document\Moderation;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\PersistentCollection;

/**
 * Class Sanction
 * @package App\Document
 * @MongoDB\Document(collection="sanctions")
 */
class Sanction
{
    /**
     * @MongoDB\Id
     */
    private string $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $memberId;

    /**
     * @MongoDB\ReferenceOne(targetDocument=ConvictedUser::class, storeAs="id")
     */
    protected ConvictedUser $user;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $type;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $moderator;

    /**
     * @MongoDB\Field(type="int")
     */
    protected int $start;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    protected ?int $duration = null;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    protected ?int $finish = null;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $reason;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected bool $revoked;

    /**
     * @MongoDB\Field(type="string")
     */
    protected string $sanctionId;

    /**
     * @MongoDB\EmbedOne(targetDocument=SanctionInformations::class)
     */
    protected SanctionInformations $informations;

    /**
     * @MongoDB\EmbedMany(targetDocument=SanctionUpdate::class)
     */
    protected PersistentCollection $updates;

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
     * @return ConvictedUser
     */
    public function getUser(): ConvictedUser
    {
        return $this->user;
    }

    /**
     * @param ConvictedUser $user
     */
    public function setUser(ConvictedUser $user): void
    {
        $this->user = $user;
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
     * @return DateTime
     */
    public function getStart(): DateTime
    {
        return date_create()->setTimestamp($this->start / 1000);
    }

    /**
     * @param DateTime $start
     */
    public function setStart(DateTime $start): void
    {
        $this->start = $start->getTimestamp();
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return DateTime|null
     */
    public function getFinish(): ?DateTime
    {
        if ($this->finish == null) return null;
        return date_create()->setTimestamp($this->finish / 1000);
    }

    /**
     * @param DateTime|null $finish
     */
    public function setFinish(?DateTime $finish): void
    {
        $this->finish = $finish->getTimestamp();
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

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * @param bool $revoked
     */
    public function setRevoked(bool $revoked): void
    {
        $this->revoked = $revoked;
    }

    /**
     * @return string
     */
    public function getSanctionId(): string
    {
        return $this->sanctionId;
    }

    /**
     * @param string $sanctionId
     */
    public function setSanctionId(string $sanctionId): void
    {
        $this->sanctionId = $sanctionId;
    }

    /**
     * @return SanctionInformations
     */
    public function getInformations(): SanctionInformations
    {
        return $this->informations;
    }

    /**
     * @param SanctionInformations $informations
     */
    public function setInformations(SanctionInformations $informations): void
    {
        $this->informations = $informations;
    }

    /**
     * @return PersistentCollection
     */
    public function getUpdates(): PersistentCollection
    {
        return $this->updates;
    }

    /**
     * @param PersistentCollection $updates
     */
    public function setUpdates(PersistentCollection $updates): void
    {
        $this->updates = $updates;
    }

}
