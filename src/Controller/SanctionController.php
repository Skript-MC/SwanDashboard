<?php

namespace App\Controller;

use App\Document\Moderation\Sanction;
use App\Entity\SanctionSearch;
use App\Form\SanctionSearchType;
use App\Repository\SanctionRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sanctions')]
#[IsGranted('ROLE_STAFF')]
class SanctionController extends AbstractController
{
    private SanctionRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(DocumentManager $dm, PaginatorInterface $paginator)
    {
        $this->repository = $dm->getRepository(Sanction::class);
        $this->paginator = $paginator;
    }

    #[Route('', name: 'sanctions')]
    public function home(Request $request): Response
    {
        $search = new SanctionSearch();
        $form = $this->createForm(SanctionSearchType::class, $search)
            ->handleRequest($request);
        return $this->render('sanctions/home.html.twig', [
            'sanctions' => $this->paginator->paginate(
                $this->repository->search($search),
                $request->query->getInt('page', 1)
            ),
            'search' => $form->createView()
        ]);
    }
}
