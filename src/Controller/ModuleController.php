<?php

namespace App\Controller;

use App\Document\Module;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use MongoDB\BSON\UTCDateTime;
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
    #[Route('', name: 'modules')]
    public function home(DocumentManager $dm): Response
    {
        return $this->render('modules/home.html.twig', [
            'modules' => $dm->getRepository(Module::class)->findAll()
        ]);
    }

    #[Route('/api', methods: ['POST'])]
    public function api(Request $request, DocumentManager $dm): Response
    {
        if (!$this->isGranted('ROLE_STAFF'))
            return new JsonResponse(['error' => 'Vous n\'avez pas la permission de modifier l\'état de ce module.'], Response::HTTP_FORBIDDEN);

        $moduleId = $request->request->getAlnum('moduleId');
        $enabled = $request->request->getBoolean('enabled');
        if (!$moduleId || !isset($enabled))
            return new JsonResponse(['error' => 'Votre requête est invalide.'], Response::HTTP_BAD_REQUEST);

        try {
            $dm->createQueryBuilder(Module::class)
                ->findAndUpdate()
                ->field('_id')->equals($moduleId)
                ->field('enabled')->set($enabled)
                ->field('modified')->set(new UTCDateTime(new DateTime()))
                ->getQuery()
                ->execute();
        } catch (MongoDBException) {
            return new JsonResponse(['error' => 'Une erreur interne est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);

    }

}
