<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class SharedConfigRepository extends DocumentRepository
{

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
