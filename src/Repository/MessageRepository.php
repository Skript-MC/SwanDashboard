<?php

namespace App\Repository;

use App\Document\Message;
use App\Document\MessageEdit;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class MessageRepository extends DocumentRepository
{
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
