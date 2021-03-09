<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\MessageEditRequest;
use App\Document\User;
use App\Service\MessageEditService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/messages')]
#[IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    #[Route('', name: 'messages:history')]
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $query = $dm->createQueryBuilder(MessageEditRequest::class)
            ->sort('_id', 'DESC');

        $editions = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('pageEditions', 1)
        );

        $requests = $paginator->paginate(
            $query->field('user')->equals($user->getId())->getQuery(),
            $request->query->getInt('pageRequests', 1)
        );

        $requests->setPaginatorOptions(['pageParameterName' => 'pageRequests']);
        $editions->setPaginatorOptions(['pageParameterName' => 'pageEditions']);

        return $this->render('messages/history.html.twig', [
            'requests' => $requests,
            'editions' => $editions
        ]);
    }

    #[Route('/auto', name: 'messages:list:auto')]
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

    #[Route('/addonpack', name: 'messages:list:addonpack')]
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

    #[Route('/error', name: 'messages:list:error')]
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

    #[Route('/new', name: 'messages:new', methods: ['GET'])]
    public function newMessage(): Response
    {
        return $this->render('messages/new.html.twig');
    }

    #[Route('/new', name: 'messages:postNew', methods: ['POST'])]
    public function postNewMessage(Request $request, DocumentManager $dm): Response
    {
        $name = $this->formatString($request->request->get('name'));
        $aliases = array_map(function (string $entry) {
            return $this->formatString($entry);
        }, $request->request->all('aliases'));
        $content = $this->formatContent(urldecode($request->request->get('content')));
        $messageType = $this->formatString($request->request->get('type'));

        if (!$name || !$aliases || !$content || !in_array($messageType, ['auto', 'error', 'addonpack'])) {
            $this->addFlash('error', 'Certains champs sont incorrects, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:history');
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
        return $this->redirectToRoute('messages:history');

    }

    public function formatString(string $input): string
    {
        $input = str_replace('\n', ' ', $input);
        $input = str_replace('\t', ' ', $input);
        return $input;
    }

    public function formatContent(string $input): string
    {
        $input = str_replace("\\n", "\n", $input);
        if (strlen($input) > 2000) $input = substr($input, 0, 1997) . '...';
        return $input;
    }

    #[Route('/edit', name: 'messages:postEdit', methods: ['POST'])]
    public function postEdit(Request $request, DocumentManager $dm): Response
    {
        $messageId = $request->request->get('messageId');

        $newName = $this->formatString($request->request->get('name'));
        $newAliases = array_map(function (string $entry) {
            return $this->formatString($entry);
        }, $request->request->all('aliases'));
        $newContent = $this->formatContent(urldecode($request->request->get('content')));
        $newType = $request->request->get('type');

        if (!$messageId || !$newName || !$newAliases || !$newContent) {
            $this->addFlash('error', 'Certains champs sont vides, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $messageId]);
        if (!$message) {
            $this->addFlash('error', 'Le message avec identifiant ' . $messageId . ' n\'a pas été trouvé dans nos bases de données.');
            return $this->redirectToRoute('messages:history', ['messageId' => $messageId]);
        }
        /** @var User $user */
        $user = $this->getUser();

        $edit = new MessageEditRequest();
        $edit->setUser($user);
        $edit->setMessage($message);
        $edit->setNewName($newName);
        $edit->setNewAliases($newAliases);
        $edit->setNewContent($newContent);
        if ($message->getMessageType() !== $newType)
            $edit->setMessageType($newType);
        $dm->persist($edit);
        $dm->flush();

        $this->addFlash('success', 'Votre suggestion de modification a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages:history');
    }

    #[Route('/view/{messageId}', name: 'messages:view')]
    public function viewEdit(Request $request, DocumentManager $dm, MessageEditService $messageService): Response
    {
        $messageRequest = $dm->getRepository(MessageEditRequest::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$messageRequest) {
            $this->addFlash('error', 'Nous n\'avons pas pu trouver de message correspondant à cet identifiant.');
            return new RedirectResponse($this->generateUrl('messages:history'));
        }
        return $this->render('messages/view.html.twig', [
            'request' => $messageRequest,
            'previous' => $messageService->getPreviousEdit($messageRequest)
        ]);
    }

    #[Route('/approve', name: 'messages:approve', methods: ['POST'])]
    public function approveEdit(Request $request, DocumentManager $dm): Response
    {
        $messageId = $request->request->get('messageId');
        $isValidated = $request->request->getBoolean('validated');
        $message = $dm->getRepository(MessageEditRequest::class)->findOneBy(['_id' => $messageId]);

        if (!$message || !isset($isValidated)) {
            $this->addFlash('error', 'Votre requête est invalide.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }

        if ($message->isValidated()) {
            $this->addFlash('error', 'Ce message a déjà été validé.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }

        /** @var User $reviewer */
        $reviewer = $this->getUser();

        /** @var User $author */
        $author = $message->getUser();

        if (!($this->isGranted('ROLE_STAFF') || ($reviewer->getId() == $author->getId() && !$isValidated))) {
            $this->addFlash('error', 'Vous n\'avez pas la permission d\'approuver ce message.' . $reviewer->getId() . '->' . $reviewer->getId());
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }
        /** @var Message $targetMessage */
        $targetMessage = $message->getMessage();

        if (!$targetMessage && $isValidated) { // It's a new message
            $newMessage = new Message();
            $newMessage->setName($message->getNewName());
            $newMessage->setAliases($message->getNewAliases());
            $newMessage->setContent($message->getNewContent());
            $newMessage->setMessageType($message->getMessageType());
            $dm->persist($newMessage);
            $dm->flush();
            $targetMessage = $newMessage;
        } else if ($isValidated) {
            $query = $dm->createQueryBuilder(Message::class)
                ->findAndUpdate()
                ->field('_id')->equals($targetMessage->getId())
                ->field('name')->set($message->getNewName())
                ->field('aliases')->set($message->getNewAliases())
                ->field('content')->set($message->getNewContent());

            if ($message->getMessageType() !== null) // A category changement was requested
                $query->field('messageType')->set($message->getMessageType());

            $query->getQuery()
                ->execute();
        }
        $dm->createQueryBuilder(MessageEditRequest::class)
            ->findAndUpdate()
            ->field('_id')->equals($message->getId())
            ->field('validated')->set($isValidated)
            ->field('reviewer')->set($reviewer)
            ->field('message')->set($targetMessage)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Cette suggestion de modification a été marquée comme ' . ($isValidated ? 'validée' : 'refusée') . '.');
        return $this->redirectToRoute('messages:history');
    }

    #[Route('/{messageId}', name: 'messages:edit', methods: ['GET'])]
    public function editMessage(Request $request, DocumentManager $dm): Response
    {
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$message) return new RedirectResponse($this->generateUrl('messages:history'));
        $messageEditRequest = $dm->createQueryBuilder(MessageEditRequest::class)
            ->field('message.id')->equals($message->getId())
            ->field('validated')->equals(null)
            ->getQuery()
            ->getSingleResult();
        return $this->render('messages/edit.html.twig', [
            'message' => $message,
            'editForbidden' => isset($messageEditRequest)
        ]);
    }

}
