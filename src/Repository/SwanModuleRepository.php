<?php

namespace App\Repository;

use DateTime;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use MongoDB\BSON\UTCDateTime;

class SwanModuleRepository extends DocumentRepository
{
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
