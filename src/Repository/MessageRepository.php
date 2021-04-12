<?php

namespace App\Repository;

use App\Document\Message;
use App\Document\MessageEdit;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

class MessageRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findOneById(string $id): ?Message
    {
        return $this->findOneBy(['_id' => $id]);
    }

    public function findByMessageType(string $messageType): Builder
    {
        return $this->createQueryBuilder()
            ->field('messageType')->equals($messageType);
    }

    public function update(Message $message, MessageEdit $messageEdit): Builder
    {
        return $this->createQueryBuilder()
            ->updateOne()
            ->field('_id')->equals($message->getId())
            ->field('name')->set($messageEdit->getNewName())
            ->field('aliases')->set($messageEdit->getNewAliases())
            ->field('content')->set($messageEdit->getNewContent());
    }
}
