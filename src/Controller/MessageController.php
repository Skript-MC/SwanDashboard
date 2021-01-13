<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\MessageEditRequest;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessageController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 * @Route("/messages")
 */
class MessageController extends AbstractController
{

    public function formatString(string $input): string
    {
        $input = strip_tags($input);
        $input = str_replace('\n', ' ', $input);
        $input = str_replace('\t', ' ', $input);
        return $input;
    }

    public function formatContent(string $input): string
    {
        $input = strip_tags($input);
        $input = str_replace("\\n", "\n", $input);
        return $input;
    }

    /**
     * @Route("", name="messages")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $requests = $paginator->paginate(
            $dm->createQueryBuilder(MessageEditRequest::class)
                ->field('user.id')->equals($user->getId())
                ->sort( '_id', 'DESC')
                ->getQuery(),
            $request->query->getInt('page', 1)
        );
        return $this->render('messages/home.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * @Route("/auto", name="messages:auto")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function automaticMessages(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Messages rapides',
            'messages' => $paginator->paginate(
                $dm->getRepository(Message::class)->findBy(['messageType' => 'auto']),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    /**
     * @Route("/addonpack", name="messages:addonpack")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function addonPacks(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Packs d\'add-ons',
            'messages' => $paginator->paginate(
                $dm->getRepository(Message::class)->findBy(['messageType' => 'addonpack']),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    /**
     * @Route("/error", name="messages:error")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function errorDetails(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Détails erreurs',
            'messages' => $paginator->paginate(
                $dm->getRepository(Message::class)->findBy(['messageType' => 'error']),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    /**
     * @Route("/new", name="messages:new", methods={"GET"})
     * @return Response
     */
    public function newMessage(): Response
    {
        return $this->render('messages/new.html.twig');
    }

    /**
     * @Route("/new", name="messages:postNew")
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     * @throws MongoDBException
     */
    public function postNewMessage(Request $request, DocumentManager $dm): Response
    {
        // Prevent XSS attacks
        $name = $this->formatString($request->request->get('name'));
        $aliases = array_map(function (string $entry) { return $this->formatString($entry); }, $request->request->all('aliases'));
        $content = $this->formatContent(urldecode($request->request->get('content')));
        $messageType = $this->formatString($request->request->get('type'));

        if (!$name || !$aliases || !$content || !in_array($messageType, ['auto', 'error', 'addonpack'])) {
            $this->addFlash('error', 'Certains champs sont incorrects, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages');
        }

        /** @var User $user */
        $user = $this->getUser();

        $request = new MessageEditRequest();
        $request->setNewName($name);
        $request->setNewAliases($aliases);
        $request->setNewContent($content);
        $request->setUser($user);
        $request->setMessageType($messageType);
        $dm->persist($request);
        $dm->flush();

        $this->addFlash('success', 'Votre suggestion de nouveau message a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages');

    }

    /**
     * @Route("/edit", name="messages:postEdit", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     * @throws MongoDBException
     */
    public function postEdit(Request $request, DocumentManager $dm): Response
    {
        $messageId = $request->request->get('messageId');

        // Prevent XSS attacks
        $newName = $this->formatString($request->request->get('name'));
        $newAliases = array_map(function (string $entry) { return $this->formatString($entry); }, $request->request->all('aliases'));
        $newContent = $this->formatContent(urldecode($request->request->get('content')));

        if (!$messageId || !$newName || !$newAliases || !$newContent) {
            $this->addFlash('error', 'Certains champs sont vides, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $messageId]);
        if (!$message) {
            $this->addFlash('error', 'Le message avec identifiant ' . $messageId . ' n\'a pas été trouvé dans nos bases de données.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        /** @var User $user */
        $user = $this->getUser();

        $edit = new MessageEditRequest();
        $edit->setUser($user);
        $edit->setMessage($message);
        $edit->setNewName($newName);
        $edit->setNewAliases($newAliases);
        $edit->setNewContent($newContent);
        $dm->persist($edit);
        $dm->flush();

        $this->addFlash('success', 'Votre suggestion de modification a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages');
    }

    /**
     * @Route("/view/{messageId}", name="messages:view")
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     */
    public function viewEdit(Request $request, DocumentManager $dm): Response
    {
        $messageRequest = $dm->getRepository(MessageEditRequest::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$messageRequest) {
            $this->addFlash('error', 'Nous n\'avons pas pu trouver de message correspondant à cet identifiant.');
            return new RedirectResponse($this->generateUrl('messages'));
        }
        /** @var User $user */
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_STAFF') && $messageRequest->getUser() != $user) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette modification.');
            return new RedirectResponse($this->generateUrl('messages'));
        }
        return $this->render('messages/view.html.twig', [
            'request' => $messageRequest
        ]);
    }

    /**
     * @Route("/waiting", name="messages:waiting")
     * @IsGranted("ROLE_STAFF")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function waitingRequests(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        $messages = $paginator->paginate(
            $dm->createQueryBuilder(MessageEditRequest::class)
                ->field('validated')->equals(null)
                ->getQuery(),
            $request->query->getInt('page', 1)
        );
        return $this->render('messages/admin.html.twig', [
            'title' => 'Messages en attente',
            'requests' => $messages
        ]);
    }

    /**
     * @Route("/accepted", name="messages:accepted")
     * @IsGranted("ROLE_STAFF")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function acceptedRequests(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        $messages = $paginator->paginate(
            $dm->createQueryBuilder(MessageEditRequest::class)
                ->field('validated')->equals(true)
                ->getQuery(),
            $request->query->getInt('page', 1)
        );
        return $this->render('messages/admin.html.twig', [
            'title' => 'Messages acceptés',
            'requests' => $messages
        ]);
    }

    /**
     * @Route("/denied", name="messages:denied")
     * @IsGranted("ROLE_STAFF")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function deniedRequests(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        $messages = $paginator->paginate(
            $dm->createQueryBuilder(MessageEditRequest::class)
                ->field('validated')->equals(false)
                ->getQuery(),
            $request->query->getInt('page', 1)
        );
        return $this->render('messages/admin.html.twig', [
            'title' => 'Messages refusés',
            'requests' => $messages
        ]);
    }

    /**
     * @Route("/approve", name="messages:approve", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     * @throws MongoDBException
     */
    public function approveEdit(Request $request, DocumentManager $dm): Response
    {
        $messageId = $request->request->get('messageId');
        $isValidated = $request->request->getBoolean('validated');
        $message = $dm->getRepository(MessageEditRequest::class)->findOneBy(['_id' => $messageId]);
        if (!$message || !isset($isValidated)) {
            $this->addFlash('error', 'Votre requête est invalide.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }
        if (!$this->isGranted('ROLE_STAFF')) {
            $this->addFlash('error', 'Vous n\'avez pas la permission d\'approuver ce message.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }
        /** @var Message $targetMessage */
        $targetMessage = $message->getMessage();
        /** @var User $reviewer */
        $reviewer = $this->getUser();

        if (!$targetMessage && $isValidated) { // It's a new message
            $newMessage = new Message();
            $newMessage->setName($message->getNewName());
            $newMessage->setAliases($message->getNewAliases());
            $newMessage->setContent($message->getNewContent());
            $newMessage->setMessageType($message->getMessageType());
            $dm->persist($newMessage);
            $dm->flush();
        } else if ($isValidated) {
            $dm->createQueryBuilder(Message::class)
                ->findAndUpdate()
                ->field('_id')->equals($targetMessage->getId())
                ->field('name')->set($message->getNewName())
                ->field('aliases')->set($message->getNewAliases())
                ->field('content')->set($message->getNewContent())
                ->getQuery()
                ->execute();
        }
        $dm->createQueryBuilder(MessageEditRequest::class)
            ->findAndUpdate()
            ->field('_id')->equals($message->getId())
            ->field('validated')->set($isValidated)
            ->field('reviewer')->set($reviewer)
            ->getQuery()
            ->execute();
        $this->addFlash('success', 'Cette suggestion de modification a été marquée comme ' . ($isValidated ? 'validée' : 'refusée') . '.');
        return $this->redirectToRoute('messages:waiting');
    }

    /**
     * @Route("/{messageId}", name="messages:edit", methods={"GET"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     */
    public function editMessage(Request $request, DocumentManager $dm): Response
    {
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$message) return new RedirectResponse($this->generateUrl('messages'));
        $messageEditRequest = $dm->createQueryBuilder(MessageEditRequest::class)
            ->field('message.id')->equals($message->getId())
            ->field('validated')->equals(null)
            ->getQuery()
            ->getSingleResult();
        return $this->render('messages/edit.html.twig', [
            'addonPack' => $message,
            'editForbidden' => isset($messageEditRequest)
        ]);
    }

}
