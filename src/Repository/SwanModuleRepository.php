<?php

namespace App\Repository;

use App\Document\SwanModule;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

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
            ->getQuery()
            ->execute();
    }

    public function disabledModules(): array
    {
        return $this->createQueryBuilder()
            ->field('enabled')->equals(false)
            ->getQuery()
            ->execute()
            ->toArray();
    }

}
