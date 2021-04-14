<?php

namespace App\Controller;

use App\Entity\SanctionSearch;
use App\Form\SanctionSearchType;
use App\Repository\SanctionRepository;
use DateInterval;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/{sanctionId}', name: 'sanctions:view')]
    public function view(SanctionRepository $repository, mixed $sanctionId): Response
    {
        $sanction = $repository->findOneById($sanctionId);
        if (!$sanction)
            return new RedirectResponse($this->generateUrl('sanctions'));
        try {
            $d1 = new DateTime();
            $d2 = new DateTime();
            $d2->add(new DateInterval('PT' . ($sanction->getDuration() / 1000 ?? 0) . 'S'));
            $dateInterval = $d2->diff($d1)->format('%m mois, %d jour(s), %h heure(s), %i minute(s) et %s seconde(s)');
        } catch (Exception) {
            $dateInterval = 'Inconnu';
        }
        return $this->render('sanctions/view.html.twig', [
            'sanction' => $sanction,
            'dateInterval' => $dateInterval
        ]);
    }
}
