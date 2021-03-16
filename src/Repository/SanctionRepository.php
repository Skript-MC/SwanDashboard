<?php

namespace App\Repository;

use App\Entity\SanctionSearch;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use MongoDB\BSON\Regex;

class SanctionRepository extends DocumentRepository
{
    public function search(SanctionSearch $sanctionSearch): Query
    {
        $query = $this->createQueryBuilder();
        if ($sanctionSearch->getMemberId())
            $query->field('memberId')->equals($sanctionSearch->getMemberId());
        if ($sanctionSearch->getModeratorId())
            $query->field('moderator')->equals($sanctionSearch->getModeratorId());
        if ($sanctionSearch->getReason())
            $query->field('reason')->equals(new Regex('/' . $sanctionSearch->getReason() . '/'));
        if ($sanctionSearch->getSanctionStatus() !== null)
            $query->field('revoked')->equals($sanctionSearch->getSanctionStatus());
        if ($sanctionSearch->getSanctionType())
            $query->field('type')->equals($sanctionSearch->getSanctionType());
        if ($sanctionSearch->getSanctionId())
            $query->field('sanctionId')->equals($sanctionSearch->getSanctionId());
        if ($sanctionSearch->getAfterDate())
            $query->field('start')->gte($sanctionSearch->getAfterDate() * 1000);
        if ($sanctionSearch->getBeforeDate())
            $query->field('start')->lte($sanctionSearch->getBeforeDate() * 1000);
        return $query->sort('_id', 'DESC')
            ->getQuery();
    }
}
