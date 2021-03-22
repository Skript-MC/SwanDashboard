<?php

namespace App\Repository;

use App\Document\SwanModule;
use DateTime;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use MongoDB\BSON\UTCDateTime;

class SwanModuleRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SwanModule::class);
    }

    public function changeModuleState(string $moduleId, bool $enabled): void
    {
        $this->createQueryBuilder()
            ->findAndUpdate()
            ->field('_id')->equals($moduleId)
            ->field('enabled')->set($enabled)
            ->field('modified')->set(new UTCDateTime(new DateTime()))
            ->getQuery()
            ->execute();
    }
}
