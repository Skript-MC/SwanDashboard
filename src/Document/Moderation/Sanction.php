<?php

namespace App\Document\Moderation;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\PersistentCollection;
use RestCord\Model\User\User;

/**
 * Class Sanction
 * @package App\Document
 * @MongoDB\Document(collection="sanctions", repositoryClass="App\Repository\SanctionRepository")
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
    private string $memberId;

    /**
     * @MongoDB\ReferenceOne(targetDocument=ConvictedUser::class, storeAs="id")
     */
    private ConvictedUser $user;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $type;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $moderator;

    /**
     * @MongoDB\Field(type="int")
     */
    private int $start;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    private ?int $duration = null;

    /**
     * @MongoDB\Field(type="int", nullable=true)
     */
    private ?int $finish = null;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $reason;

    /**
     * @MongoDB\Field(type="bool")
     */
    private bool $revoked;

    /**
     * @MongoDB\Field(type="string")
     */
    private string $sanctionId;

    /**
     * @MongoDB\EmbedOne(targetDocument=Informations::class)
     */
    private Informations $informations;

    /**
     * @MongoDB\EmbedMany(targetDocument=Update::class)
     */
    private Collection $updates;

    private User $moderatorUser;
    private User $memberUser;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Sanction
     */
    public function setId(string $id): Sanction
    {
        $this->id = $id;
        return $this;
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
     * @return Sanction
     */
    public function setMemberId(string $memberId): Sanction
    {
        $this->memberId = $memberId;
        return $this;
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
     * @return Sanction
     */
    public function setUser(ConvictedUser $user): Sanction
    {
        $this->user = $user;
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
     * @return Sanction
     */
    public function setType(string $type): Sanction
    {
        $this->type = $type;
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
     * @return Sanction
     */
    public function setModerator(string $moderator): Sanction
    {
        $this->moderator = $moderator;
        return $this;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @param int $start
     * @return Sanction
     */
    public function setStart(int $start): Sanction
    {
        $this->start = $start;
        return $this;
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
     * @return Sanction
     */
    public function setDuration(?int $duration): Sanction
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFinish(): ?int
    {
        return $this->finish;
    }

    /**
     * @param int|null $finish
     * @return Sanction
     */
    public function setFinish(?int $finish): Sanction
    {
        $this->finish = $finish;
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
     * @return Sanction
     */
    public function setReason(string $reason): Sanction
    {
        $this->reason = $reason;
        return $this;
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
     * @return Sanction
     */
    public function setRevoked(bool $revoked): Sanction
    {
        $this->revoked = $revoked;
        return $this;
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
     * @return Sanction
     */
    public function setSanctionId(string $sanctionId): Sanction
    {
        $this->sanctionId = $sanctionId;
        return $this;
    }

    /**
     * @return Informations
     */
    public function getInformations(): Informations
    {
        return $this->informations;
    }

    /**
     * @param Informations $informations
     * @return Sanction
     */
    public function setInformations(Informations $informations): Sanction
    {
        $this->informations = $informations;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUpdates(): Collection
    {
        return $this->updates;
    }

    /**
     * @param Collection $updates
     * @return Sanction
     */
    public function setUpdates(Collection $updates): Sanction
    {
        $this->updates = $updates;
        return $this;
    }
}
