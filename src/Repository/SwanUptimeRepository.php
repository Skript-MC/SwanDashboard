<?php

namespace App\Repository;

use App\Document\SwanUptime;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class SwanUptimeRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SwanUptime::class);
    }

    public function uptimeOfToday(): ?SwanUptime
    {
        dump(date_create()->format('d-m-Y'));
        return $this->findOneBy(['day' => date_create()->format('d-m-Y')]);
    }
}
