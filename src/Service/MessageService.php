<?php

namespace App\Service;

use App\Document\MessageEditRequest;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class MessageService
{
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function getPreviousEdit(MessageEditRequest $currentEdit): ?MessageEditRequest
    {
        if ($currentEdit->getMessage() == null) return $currentEdit;
        /** @var MessageEditRequest $result */
        $result = $this->documentManager->createQueryBuilder(MessageEditRequest::class)
            ->field('_id')->lt(new ObjectId($currentEdit->getId()))
            ->field('message.id')->equals($currentEdit->getMessage()->getId())
            ->field('validated')->equals(true)
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
        return $result;
    }

}
