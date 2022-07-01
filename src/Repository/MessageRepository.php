<?php

namespace App\Repository;

use App\Document\Message;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class MessageRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findById(string $id, string $type): ?Message
    {
        return $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->field('messageType')->equals($type)
            ->getQuery()
            ->getSingleResult();
    }

    public function findByName(string $name): ?Message
    {
        return $this->createQueryBuilder()
            ->field('name')->equals($name)
            ->getQuery()
            ->getSingleResult();
    }

    public function findQuickReplies(): array
    {
        return $this->createQueryBuilder()
            ->field('messageType')->equals('quickReply')
            ->getQuery()
            ->toArray();
    }

    public function findErrorInfos(): array
    {
        return $this->createQueryBuilder()
            ->field('messageType')->equals('errorInfo')
            ->getQuery()
            ->toArray();
    }

    public function findRules(): array
    {
        return $this->createQueryBuilder()
            ->field('messageType')->equals('rule')
            ->getQuery()
            ->toArray();
    }

    public function findJokes(): array
    {
        return $this->createQueryBuilder()
            ->field('messageType')->equals('joke')
            ->getQuery()
            ->toArray();
    }
}
