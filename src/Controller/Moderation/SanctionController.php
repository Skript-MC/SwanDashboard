<?php

namespace App\Controller\Moderation;

use App\Repository\MessageRepository;
use App\Repository\SanctionRepository;
use App\Service\SanctionService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/moderation/sanctions')]
class SanctionController extends AbstractController
{
    #[Route('', name: 'moderation_sanctions')]
    public function sanctions(SanctionRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('moderation/sanctions.html.twig', [
            'sanctions' => $paginator->paginate(
                $repository->findAll(),
                $request->query->getInt('page', 1),
                10
            ),
        ]);
    }
}
