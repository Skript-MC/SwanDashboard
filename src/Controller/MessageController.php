<?php

namespace App\Controller;

use App\Document\Message;
use App\Document\MessageEdit;
use App\Document\DiscordUser;
use App\Repository\MessageEditRepository;
use App\Repository\MessageRepository;
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
    private DocumentManager $dm;
    private MessageEditRepository $messageEditRepository;
    private MessageRepository $messageRepository;
    private PaginatorInterface $paginator;

    public function __construct(DocumentManager $dm, PaginatorInterface $paginator)
    {
        $this->dm = $dm;
        $this->messageEditRepository = $dm->getRepository(MessageEdit::class);
        $this->messageRepository = $dm->getRepository(Message::class);
        $this->paginator = $paginator;
    }

    private function formatString(string $input): string
    {
        $input = str_replace('\n', ' ', $input);
        $input = str_replace('\t', ' ', $input);
        return $input;
    }

    private function formatContent(string $input): string
    {
        $input = str_replace("\\n", "\n", $input);
        if (strlen($input) > 2000) $input = substr($input, 0, 1997) . '...';
        return $input;
    }

    #[Route('', name: 'messages:logs')]
    public function home(Request $request): Response
    {
        /** @var DiscordUser $user */
        $user = $this->getUser();
        $editions = $this->paginator->paginate(
            $this->messageEditRepository->findAllEdits(),
            $request->query->getInt('pageEditions', 1),
            10,
            ['pageParameterName' => 'pageRequests']
        );
        $requests = $this->paginator->paginate(
            $this->messageEditRepository->findAllEditsByUser($user->getId()),
            $request->query->getInt('pageRequests', 1),
            10,
            ['pageParameterName' => 'pageEditions']
        );
        return $this->render('messages/logs.html.twig', [
            'requests' => $requests,
            'editions' => $editions
        ]);
    }

    #[Route('/auto', name: 'messages:list:auto')]
    public function automaticMessages(Request $request): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Messages rapides',
            'messages' => $this->paginator->paginate(
                $this->messageRepository->findByMessageType('auto'),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/addonpack', name: 'messages:list:addonpack')]
    public function addonPacks(Request $request): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Packs d\'add-ons',
            'messages' => $this->paginator->paginate(
                $this->messageRepository->findByMessageType('addonpack'),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/error', name: 'messages:list:error')]
    public function errorDetails(Request $request): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Détails erreurs',
            'messages' => $this->paginator->paginate(
                $this->messageRepository->findByMessageType('error'),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/rules', name: 'messages:list:rule')]
    public function rules(Request $request): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Règles',
            'messages' => $this->paginator->paginate(
                $this->messageRepository->findByMessageType('rule'),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/jokes', name: 'messages:list:joke')]
    public function jokes(Request $request): Response
    {
        return $this->render('messages/list.html.twig', [
            'title' => 'Blagues',
            'messages' => $this->paginator->paginate(
                $this->messageRepository->findByMessageType('joke'),
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
    public function postNewMessage(Request $request): Response
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
        $this->dm->persist($request);
        $this->dm->flush();
        $this->addFlash('success', 'Votre suggestion de nouveau message a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages:logs');
    }

    #[Route('/edit', name: 'messages:postEdit', methods: ['POST'])]
    public function postEdit(Request $request): Response
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
        $message = $this->messageRepository->findOneById($messageId);
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
        $this->dm->persist($edit);
        $this->dm->flush();
        $this->addFlash('success', 'Votre suggestion de modification a été enregistrée. Elle sera traitée prochainement !');
        return $this->redirectToRoute('messages:logs');
    }

    #[Route('/view/{messageId}', name: 'messages:view')]
    public function viewEdit(Request $request): Response
    {
        $messageRequest = $this->messageEditRepository->findOneById($request->get('messageId'));
        if (!$messageRequest) {
            $this->addFlash('error', 'Nous n\'avons pas pu trouver de message correspondant à cet identifiant.');
            return new RedirectResponse($this->generateUrl('messages:logs'));
        }
        return $this->render('messages/view.html.twig', [
            'request' => $messageRequest,
            'previous' => $this->messageEditRepository->getPreviousEdit($messageRequest)
        ]);
    }

    #[Route('/approve', name: 'messages:approve', methods: ['POST'])]
    public function approveEdit(Request $request): Response
    {
        $messageId = $request->request->get('messageId');
        $isValidated = $request->request->getBoolean('validated');
        $message = $this->messageEditRepository->findOneById($messageId);
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
        /** @var Message $targetMessage */
        $targetMessage = $message->getMessage();
        if (!($this->isGranted('ROLE_STAFF') || ($reviewer->getId() == $author->getId() && !$isValidated))) {
            $this->addFlash('error', 'Vous n\'avez pas la permission d\'approuver ce message. ' . $reviewer->getId() . ' -> ' . $reviewer->getId());
            return $this->redirectToRoute('messages:view', ['messageId' => $messageId]);
        }
        if (!$isValidated) {
            $this->addFlash('success', 'Cette suggestion a bien été marquée comme refusée.');
            $this->messageEditRepository->updateEditStatus($message, false, $reviewer, $targetMessage);
            return $this->redirectToRoute('messages:logs');
        }
        if (!$targetMessage) { // It's a new message
            $newMessage = new Message();
            $newMessage->setName($message->getNewName());
            $newMessage->setAliases($message->getNewAliases());
            $newMessage->setContent($message->getNewContent());
            $newMessage->setMessageType($message->getMessageType());
            $this->dm->persist($newMessage);
            $this->dm->flush();
            $this->messageEditRepository->updateEditStatus($message, $isValidated, $reviewer, $newMessage);
            $this->addFlash('success', 'Cette suggestion a été approuvée, et le message a été créé. Il est donc désormais utilisable avec Swan.');
            return $this->redirectToRoute('messages:logs');
        }
        if ($message->getNewContent() === '') { // It's a deletion request
            $this->messageEditRepository->removeByMessage($targetMessage);
            $this->messageEditRepository->updateEditStatus($message, $isValidated, $reviewer, $targetMessage);
            $this->addFlash('success', 'Cette suggestion a été approuvée, et les messages associés ont bien été supprimés.');
            return $this->redirectToRoute('messages:logs');
        }
        $query = $this->dm->getRepository(Message::class)->update($targetMessage, $message);
        if ($message->getMessageType() !== null) // A category changement was requested
            $query->field('messageType')->set($message->getMessageType());
        $query->getQuery()->execute();
        $this->messageEditRepository->updateEditStatus($message, $isValidated, $reviewer, $targetMessage);
        $this->addFlash('success', 'Cette suggestion a été approuvée, et le message a été mis à jour.');
        return $this->redirectToRoute('messages:logs');
    }

    #[Route('/{messageId}', name: 'messages:edit', methods: ['GET'])]
    public function editMessage(Request $request): Response
    {
        $message = $this->messageRepository->findOneById($request->get('messageId'));
        if (!$message)
            return new RedirectResponse($this->generateUrl('messages:logs'));
        $messageEditRequest = $this->messageEditRepository->getPendingEditForMessage($message);
        return $this->render('messages/edit.html.twig', [
            'message' => $message,
            'editForbidden' => isset($messageEditRequest)
        ]);
    }
}
