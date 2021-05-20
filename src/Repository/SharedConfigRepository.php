<?php

namespace App\Repository;

use App\Document\SharedConfig;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class SharedConfigRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedConfig::class);
    }

    public function updateLoggedChannels(array $loggedChannels): void
    {
        $this->createQueryBuilder()
            ->findAndUpdate()
            ->field('name')->equals('logged-channels')
            ->field('value')->set($loggedChannels)
            ->getQuery()
            ->execute();
    }

    public function getLoggedChannels(): ?object
    {
        return $this->createQueryBuilder()
            ->field('name')->equals('logged-channels')
            ->getQuery()
            ->getSingleResult();
    }
}
