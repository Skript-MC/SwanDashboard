<?php

namespace App\Repository;

use App\Document\Moderation\Sanction;
use App\Entity\SanctionSearch;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Query;
use MongoDB\BSON\Regex;

class SanctionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sanction::class);
    }

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

    public function findOneById($id): Sanction|null
    {
        return $this->findOneBy(['_id' => $id]);
    }
}
