<?php

namespace App\Controller;

use App\Entity\SanctionSearch;
use App\Form\SanctionSearchType;
use App\Repository\SanctionRepository;
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
    #[Route('', name: 'sanctions')]
    public function home(Request $request, PaginatorInterface $paginator, SanctionRepository $sanctionRepository): Response
    {
        $search = new SanctionSearch();
        $form = $this->createForm(SanctionSearchType::class, $search)
            ->handleRequest($request);
        return $this->render('sanctions/home.html.twig', [
            'sanctions' => $paginator->paginate(
                $sanctionRepository->search($search),
                $request->query->getInt('page', 1)
            ),
            'search' => $form->createView()
        ]);
    }
}
