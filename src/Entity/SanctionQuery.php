<?php

namespace App\Entity;

/**
 * Class SanctionQuery
 * @package App\Entity
 */
class SanctionQuery
{

    protected ?string $memberId = null;

    protected ?string $sanctionStatus = null;

    protected ?string $moderatorId = null;

    protected ?int $afterDate = null;

    protected ?int $beforeDate = null;

    protected ?string $reason = null;

    protected ?string $sanctionType = null;

    protected ?string $sanctionId = null;

    /**
     * @return string|null
     */
    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    /**
     * @param string|null $memberId
     */
    public function setMemberId(?string $memberId): void
    {
        $this->memberId = $memberId;
    }

    /**
     * @return string|null
     */
    public function getSanctionStatus(): ?string
    {
        return $this->sanctionStatus;
    }

    /**
     * @param string|null $sanctionStatus
     */
    public function setSanctionStatus(?string $sanctionStatus): void
    {
        $this->sanctionStatus = $sanctionStatus;
    }

    /**
     * @return string|null
     */
    public function getModeratorId(): ?string
    {
        return $this->moderatorId;
    }

    /**
     * @param string|null $moderatorId
     */
    public function setModeratorId(?string $moderatorId): void
    {
        $this->moderatorId = $moderatorId;
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
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     */
    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return string|null
     */
    public function getSanctionType(): ?string
    {
        return $this->sanctionType;
    }

    /**
     * @param string|null $sanctionType
     */
    public function setSanctionType(?string $sanctionType): void
    {
        $this->sanctionType = $sanctionType;
    }

    /**
     * @return string|null
     */
    public function getSanctionId(): ?string
    {
        return $this->sanctionId;
    }

    /**
     * @param string|null $sanctionId
     */
    public function setSanctionId(?string $sanctionId): void
    {
        $this->sanctionId = $sanctionId;
    }

}
