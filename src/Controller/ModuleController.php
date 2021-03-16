<?php

namespace App\Controller;

use App\Document\SwanModule;
use App\Repository\SwanModuleRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modules')]
#[IsGranted('ROLE_USER')]
class ModuleController extends AbstractController
{
    private SwanModuleRepository $repository;

    public function __construct(DocumentManager $dm)
    {
        $this->repository = $dm->getRepository(SwanModule::class);
    }

    #[Route('', name: 'modules')]
    public function home(): Response
    {
        return $this->render('modules/home.html.twig', [
            'modules' => $this->repository->findAll()
        ]);
    }

    #[Route('/api', methods: ['POST'])]
    public function api(Request $request): Response
    {
        if (!$this->isGranted('ROLE_STAFF'))
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier l\'état de ce module.'], Response::HTTP_FORBIDDEN);
        $moduleId = $request->request->getAlnum('moduleId');
        $enabled = $request->request->getBoolean('enabled');
        if (!$moduleId || !isset($enabled))
            return new JsonResponse(['error' => 'Votre requête est invalide.'], Response::HTTP_BAD_REQUEST);
        $this->repository->changeModuleState($moduleId, $enabled);
        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }
}
