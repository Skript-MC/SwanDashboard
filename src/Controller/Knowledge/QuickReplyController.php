<?php

namespace App\Controller\Knowledge;

use App\Document\DashUser;
use App\Exception\MessageServiceException;
use App\Repository\MessageEditRepository;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/knowledge/quickreplies')]
class QuickReplyController extends AbstractController
{
    #[Route('/', name: 'knowledge_quickreplies')]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('knowledge_quickreplies_list');
    }

    #[Route('/list', name: 'knowledge_quickreplies_list')]
    public function list(MessageRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('knowledge/quickReplies/list.html.twig', [
            'messages' => $paginator->paginate(
                $repository->findQuickReplies(),
                $request->query->getInt('page', 1),
                10
            ),
        ]);
    }

    #[Route('/new', name: 'knowledge_quickreplies_new', methods: ['GET'])]
    public function new(): Response
    {
        return $this->render('knowledge/quickreplies/new.html.twig');
    }

    #[Route('/create', name: 'knowledge_quickreplies_create', methods: ['POST'])]
    public function create(Request $request, MessageService $messageService): Response
    {
        $name = $request->request->get('name');
        $content = $request->request->get('content');
        $aliases = json_decode($request->request->get('aliases'));
        if (null === $name || null === $content || null === $aliases)
            return new Response('One of the required field is null', Response::HTTP_BAD_REQUEST);
        /** @var DashUser $user */
        $user = $this->getUser();
        try {
            $edit = $messageService->createEdit("quickReply", $name, $aliases, $content, $user);
        } catch (MessageServiceException $e) {
            return new Response($e->getMessage(), $e->getHttpCode());
        }
        return new JsonResponse(['messageEditId' => $edit->getId()], Response::HTTP_CREATED);
    }

    #[Route('/review/{messageId}', name: 'knowledge_quickreplies_review', methods: ['GET'])]
    public function review(string $messageId, MessageEditRepository $repository): Response
    {
        $edit = $repository->findById($messageId, "quickReply");
        if (null === $edit) {
            $this->addFlash('error', "Ce message n'a pas été trouvé dans notre base de données.");
            return $this->redirectToRoute('knowledge_feed');
        }
        return $this->render('knowledge/quickreplies/review.html.twig', [
            'edit' => $edit,
        ]);
    }

    #[Route('/review/{messageId}', name: 'knowledge_quickreplies_validate', methods: ['POST'])]
    public function validate(string $messageId, MessageEditRepository $repository, Request $request, MessageService $messageService): Response
    {
        $edit = $repository->findById($messageId, "quickReply");
        if (null === $edit) {
            $this->addFlash('error', "Ce message n'a pas été trouvé dans notre base de données.");
            return $this->redirectToRoute('knowledge_feed');
        }
        switch ($request->request->get('action')) {
            case "approve":
                $messageService->acceptEdit($this->getUser(), $edit);
                break;
            case "refuse":
                $messageService->refuseEdit($this->getUser(), $edit);
                break;
        }
        return $this->redirectToRoute('knowledge_quickreplies_review', ['messageId' => $messageId]);
    }

    #[Route('/edit/{messageId}', name: 'knowledge_quickreplies_editor', methods: ['GET'])]
    public function editor(string $messageId, MessageRepository $repository): Response
    {
        return $this->render('knowledge/quickreplies/edit.html.twig', [
            'message' => $repository->findById($messageId, "quickReply"),
        ]);
    }

    #[Route('/edit/{messageId}', name: 'knowledge_quickreplies_edit', methods: ['POST'])]
    public function edit(string $messageId, MessageRepository $repository, Request $request, MessageService $messageService): Response
    {
        $name = $request->request->get('name');
        $content = $request->request->get('content');
        $aliases = json_decode($request->request->get('aliases'));
        if (null === $name || null === $content || null === $aliases)
            return new Response('One of the required field is null', Response::HTTP_BAD_REQUEST);
        /** @var DashUser $user */
        $user = $this->getUser();

        $message = $repository->findById($messageId, "quickReply");
        if (null === $message)
            return new Response('Message not found', Response::HTTP_NOT_FOUND);

        try {
            $edit = $messageService->editMessage($message, $name, $aliases, $content, $user);
        } catch (MessageServiceException $e) {
            return new Response($e->getMessage(), $e->getHttpCode());
        }
        return new JsonResponse(['messageEditId' => $edit->getId()], Response::HTTP_CREATED);
    }
}
