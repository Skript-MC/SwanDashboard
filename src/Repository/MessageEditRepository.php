<?php

namespace App\Repository;

use App\Document\Message;
use App\Document\MessageEdit;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class MessageEditRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageEdit::class);
    }

    public function findById(string $id, string $type): ?MessageEdit
    {
        return $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->field('messageType')->equals($type)
            ->getQuery()
            ->getSingleResult();
    }

    public function findPendingEdit(Message $message): ?MessageEdit
    {
        return $this->createQueryBuilder()
            ->field('message')->equals($message->getId())
            ->field('validated')->equals(null)
            ->getQuery()
            ->getSingleResult();
    }

    public function findRecent(): array
    {
        return $this->createQueryBuilder()
            ->sort('createdAt', 'desc')
            ->getQuery()
            ->toArray();
    }
}
