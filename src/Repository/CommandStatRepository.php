<?php

namespace App\Repository;

use App\Document\CommandStat;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class CommandStatRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandStat::class);
    }

    public function getSorted(): array
    {
        return $this->createQueryBuilder()
            ->sort('uses', 'desc')
            ->getQuery()
            ->execute()
            ->toArray();
    }
}
