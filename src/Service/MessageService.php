<?php

namespace App\Service;

use App\Document\MessageEdit;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

class MessageService
{
    private DocumentManager $documentManager;
    private MessageEdit $emptyEdit;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        $this->emptyEdit = new MessageEdit();
        $this->emptyEdit->setNewName('Sans nom');
        $this->emptyEdit->setNewAliases([]);
        $this->emptyEdit->setNewContent('');
        $this->emptyEdit->setMessageType('');
    }

    public function getPreviousEdit(MessageEdit $currentEdit): ?MessageEdit
    {
        if ($currentEdit->getMessage() == null) return $this->emptyEdit;
        /** @var MessageEdit $result */
        $result = $this->documentManager->createQueryBuilder(MessageEdit::class)
            ->field('_id')->lt(new ObjectId($currentEdit->getId()))
            ->field('message')->equals($currentEdit->getMessage()->getId())
            ->field('validated')->equals(true)
            ->limit(1)
            ->getQuery()
            ->getSingleResult();
        return $result ?? $this->emptyEdit;
    }

}
