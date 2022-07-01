<?php

namespace App\Service;

use App\Document\DiscordUser;
use App\Document\Message;
use App\Document\MessageEdit;
use App\Exceptions\MessageServiceException;
use App\Repository\MessageEditRepository;
use App\Repository\MessageRepository;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\HttpFoundation\Response;

class MessageService
{
    private MessageRepository $messageRepository;
    private MessageEditRepository $messageEditRepository;
    private DocumentManager $documentManager;

    public function __construct(MessageRepository $messageRepository, MessageEditRepository $messageEditRepository, DocumentManager $documentManager)
    {
        $this->messageRepository = $messageRepository;
        $this->messageEditRepository = $messageEditRepository;
        $this->documentManager = $documentManager;
    }

    /**
     * @throws MessageServiceException
     * @throws MongoDBException
     */
    public function createEdit(string $messageType, string $name, array $aliases, string $content, DiscordUser $user): MessageEdit
    {
        $edit = (new MessageEdit())
            ->setMessageType($messageType)
            ->setNewName($name)
            ->setNewContent($content)
            ->setNewAliases($aliases)
            ->setUser($user)
            ->setCreatedAt(new DateTime());

        // Return false if the new message is a duplicate
        $existingMessage = $this->messageRepository->findByName($edit->getNewName());
        if (null !== $existingMessage)
            throw new MessageServiceException('The new message is a duplicate', Response::HTTP_BAD_REQUEST);

        $this->documentManager->persist($edit);
        $this->documentManager->flush();
        return $edit;
    }


    /**
     * @throws MessageServiceException
     * @throws MongoDBException
     */
    public function editMessage(Message $message, string $name, array $aliases, string $content, DiscordUser $user): MessageEdit
    {
        $edit = (new MessageEdit())
            ->setMessageType($message->getMessageType())
            ->setMessage($message)
            ->setNewName($name)
            ->setNewContent($content)
            ->setNewAliases($aliases)
            ->setUser($user)
            ->setCreatedAt(new DateTime());

        $existingEdit = $this->messageEditRepository->findPendingEdit($message);
        if (null !== $existingEdit)
            throw new MessageServiceException('There is already a pending edit for this message', Response::HTTP_BAD_REQUEST);

        $this->documentManager->persist($edit);
        $this->documentManager->flush();
        return $edit;
    }

    /**
     * @throws MongoDBException
     */
    public function acceptEdit(DiscordUser $user, MessageEdit $edit): MessageEdit
    {
        $edit->setValidated(true)
            ->setReviewer($user)
            ->setReviewedAt(new DateTime());
        $this->documentManager->persist($edit);

        if ($edit->getMessage() === null) {
            $message = $edit->toMessage();
        } else {
            $message = $edit->getMessage();
            $message->setName($edit->getNewName())
                ->setAliases($edit->getNewAliases())
                ->setContent($edit->getNewContent());
        }
        $this->documentManager->persist($message);

        $this->documentManager->flush();
        return $edit;
    }

    /**
     * @throws MongoDBException
     */
    public function refuseEdit(DiscordUser $user, MessageEdit $edit): MessageEdit
    {
        $edit->setValidated(false)
            ->setReviewer($user)
            ->setReviewedAt(new DateTime());
        $this->documentManager->persist($edit);
        $this->documentManager->flush();
        return $edit;
    }
}
