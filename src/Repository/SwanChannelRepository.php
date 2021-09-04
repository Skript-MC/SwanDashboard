<?php

namespace App\Repository;

use App\Document\SwanChannel;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class SwanChannelRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SwanChannel::class);
    }

    public function findLoggedChannels(): array
    {
        return $this->findBy(['logged' => true]);
    }

    public function findChannelById(string $channelId): ?SwanChannel
    {
        return $this->findOneBy(['channelId' => $channelId]);
    }

}
