<?php

namespace App\Controller;

use App\Repository\MessageEditRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/knowledge')]
//#[IsGranted('ROLE_USER')]
class KnowledgeController extends AbstractController
{

    #[Route('/', name: 'knowledge')]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('knowledge_feed');
    }

    #[Route('/feed', name: 'knowledge_feed')]
    public function feed(MessageEditRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('knowledge/feed.html.twig', [
            'edits' => $paginator->paginate(
                $repository->findRecent(),
                $request->query->getInt('page', 1),
                10
            ),
        ]);
    }
}
