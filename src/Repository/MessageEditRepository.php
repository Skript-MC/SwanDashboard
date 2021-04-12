<?php

namespace App\Repository;

use App\Document\DiscordUser;
use App\Document\Message;
use App\Document\MessageEdit;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use MongoDB\BSON\ObjectId;

class MessageEditRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageEdit::class);
    }

    public function getPendingEditForMessage(Message $message): ?object
    {
        return $this->createQueryBuilder()
            ->field('message')->equals($message->getId())
            ->field('validated')->equals(null)
            ->getQuery()
            ->getSingleResult();
    }

    public function getPreviousEdit(MessageEdit $currentEdit): ?MessageEdit
    {
        if ($currentEdit->getMessage() == null)
            return MessageEdit::getEmptyEdit();
        /** @var MessageEdit $result */
        $result = $this->createQueryBuilder()
            ->field('_id')->lt(new ObjectId($currentEdit->getId()))
            ->field('message')->equals($currentEdit->getMessage()->getId())
            ->field('validated')->equals(true)
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
        return $result ?? MessageEdit::getEmptyEdit();
    }

    public function updateEditStatus(MessageEdit $message, bool $isValidated, DiscordUser $reviewer, Message $targetMessage)
    {
        $this->createQueryBuilder()
            ->updateOne()
            ->field('_id')->equals($message->getId())
            ->field('validated')->set($isValidated)
            ->field('reviewer')->set($reviewer)
            ->field('message')->set($targetMessage)
            ->getQuery()
            ->execute();
    }

    public function removeByMessage(Message $message): void
    {
        $this->createQueryBuilder()
            ->remove()
            ->field('message')->equals($message->getId())
            ->getQuery()
            ->execute();
        $this->createQueryBuilder()
            ->remove()
            ->field('_id')->equals($message->getId())
            ->getQuery()
            ->execute();
    }

    public function findOneById(string $id): ?MessageEdit
    {
        return $this->findOneBy(['_id' => $id]);
    }

    public function findAllEdits(): Builder
    {
        return $this->createQueryBuilder()
            ->sort('_id', 'DESC');
    }

    public function findAllEditsByUser(string $userId): Builder
    {
        return $this->findAllEdits()
            ->field('user')->equals($userId);
    }
}
