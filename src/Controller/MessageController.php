<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\MessageEdit;
use App\Document\DiscordUser;
use App\Service\MessageService;
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
    #[Route('', name: 'messages:logs')]
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        /** @var DiscordUser $user */
        $user = $this->getUser();
        $query = $dm->createQueryBuilder(MessageEdit::class)
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

        return $this->render('messages/logs.html.twig', [
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

    #[Route('/rules', name: 'messages:list:rule')]
    public function rules(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Règles',
            'messages' => $paginator->paginate(
                $dm->getRepository(Message::class)->findBy(['messageType' => 'rule']),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/jokes', name: 'messages:list:joke')]
    public function jokes(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Blagues',
            'messages' => $paginator->paginate(
                $dm->getRepository(Message::class)->findBy(['messageType' => 'joke']),
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

        if (!$name || !$content || !in_array($messageType, ['auto', 'error', 'addonpack', 'rule', 'joke'])) {
            $this->addFlash('error', 'Certains champs sont incorrects, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:logs');
        }

        /** @var DiscordUser $user */
        $user = $this->getUser();

        $request = new MessageEdit();
        $request->setNewName($name);
        $request->setNewAliases($aliases);
        $request->setNewContent($content);
        $request->setUser($user);
        $request->setMessageType($messageType);
        $dm->persist($request);
        $dm->flush();

        $this->addFlash('success', 'Votre suggestion de nouveau message a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages:logs');

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

        if (!$messageId || !$newName) {
            $this->addFlash('error', 'Certains champs sont vides, merci de réessayer votre édition.');
            return $this->redirectToRoute('messages:edit', ['messageId' => $messageId]);
        }
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $messageId]);
        if (!$message) {
            $this->addFlash('error', 'Le message avec identifiant ' . $messageId . ' n\'a pas été trouvé dans nos bases de données.');
            return $this->redirectToRoute('messages:logs', ['messageId' => $messageId]);
        }
        /** @var DiscordUser $user */
        $user = $this->getUser();

        $edit = new MessageEdit();
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
        return $this->redirectToRoute('messages:logs');
    }

    #[Route('/view/{messageId}', name: 'messages:view')]
    public function viewEdit(Request $request, DocumentManager $dm, MessageService $messageService): Response
    {
        $messageRequest = $dm->getRepository(MessageEdit::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$messageRequest) {
            $this->addFlash('error', 'Nous n\'avons pas pu trouver de message correspondant à cet identifiant.');
            return new RedirectResponse($this->generateUrl('messages:logs'));
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
        $message = $dm->getRepository(MessageEdit::class)->findOneBy(['_id' => $messageId]);

        if (!$message || !isset($isValidated)) {
            $this->addFlash('error', 'Votre requête est invalide.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }

        if ($message->isValidated()) {
            $this->addFlash('error', 'Ce message a déjà été validé.');
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }

        /** @var DiscordUser $reviewer */
        $reviewer = $this->getUser();

        /** @var DiscordUser $author */
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
            if ($message->getNewContent() === '') { // It's a deletion request
                $dm->createQueryBuilder(MessageEdit::class)
                    ->remove()
                    ->field('message')->equals($targetMessage->getId())
                    ->getQuery()
                    ->execute();
                $dm->createQueryBuilder(Message::class)
                    ->remove()
                    ->field('_id')->equals($targetMessage->getId())
                    ->getQuery()
                    ->execute();
            } else {
                $query = $dm->createQueryBuilder(Message::class)
                    ->updateOne()
                    ->field('_id')->equals($targetMessage->getId())
                    ->field('name')->set($message->getNewName())
                    ->field('aliases')->set($message->getNewAliases())
                    ->field('content')->set($message->getNewContent());

                if ($message->getMessageType() !== null) // A category changement was requested
                    $query->field('messageType')->set($message->getMessageType());

                $query->getQuery()->execute();
            }
        }
        $dm->createQueryBuilder(MessageEdit::class)
            ->updateOne()
            ->field('_id')->equals($message->getId())
            ->field('validated')->set($isValidated)
            ->field('reviewer')->set($reviewer)
            ->field('message')->set($targetMessage)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Cette suggestion de modification a été marquée comme ' . ($isValidated ? 'validée' : 'refusée') . '.');
        return $this->redirectToRoute('messages:logs');
    }

    #[Route('/{messageId}', name: 'messages:edit', methods: ['GET'])]
    public function editMessage(Request $request, DocumentManager $dm): Response
    {
        $message = $dm->getRepository(Message::class)->findOneBy(['_id' => $request->get('messageId')]);
        if (!$message) return new RedirectResponse($this->generateUrl('messages:logs'));
        $messageEditRequest = $dm->createQueryBuilder(MessageEdit::class)
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
