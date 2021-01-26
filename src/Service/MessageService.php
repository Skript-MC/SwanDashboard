<?php

namespace App\Service;

use App\Document\MessageEditRequest;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class MessageService
{
    private DocumentManager $documentManager;
    private MessageEditRequest $emptyEdit;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        $this->emptyEdit = new MessageEditRequest();
        $this->emptyEdit->setNewName('Sans nom');
        $this->emptyEdit->setNewAliases([]);
        $this->emptyEdit->setNewContent('');
        $this->emptyEdit->setMessageType('');
    }

    public function getPreviousEdit(MessageEditRequest $currentEdit): ?MessageEditRequest
    {
        if ($currentEdit->getMessage() == null) return $this->emptyEdit;
        /** @var MessageEditRequest $result */
        $result = $this->documentManager->createQueryBuilder(MessageEditRequest::class)
            ->field('_id')->lt(new ObjectId($currentEdit->getId()))
            ->field('message.id')->equals($currentEdit->getMessage()->getId())
            ->field('validated')->equals(true)
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
        return $result ?? $this->emptyEdit;
    }

}
