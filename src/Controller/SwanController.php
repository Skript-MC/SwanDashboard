<?php

namespace App\Controller;

use App\Service\ModuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/swan')]
class SwanController extends AbstractController
{

    #[Route('/', name: 'swan')]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('swan_modules');
    }

    #[Route('/modules', name: 'swan_modules')]
    public function modules(ModuleService $moduleService): Response
    {
        return $this->render('swan/modules.html.twig', [
            'moduleList' => $moduleService->getModules()
        ]);
    }

    #[Route('/modules/toggle', name: 'swan_modules_toggle')]
    public function toggleModule(Request $request, ModuleService $moduleService): JsonResponse
    {
        $moduleId = $request->request->get('moduleId');
        $status = $request->request->getBoolean('status');
        if (null === $moduleId || null === $status)
            return new JsonResponse('One of the required field is null', Response::HTTP_BAD_REQUEST);

        $module = $moduleService->toggleModule($moduleId, $status);
        if (null === $module)
            return new JsonResponse('Module not found', Response::HTTP_NOT_FOUND);

        return new JsonResponse(['status' => $module->isEnabled()], Response::HTTP_OK);
    }

}
