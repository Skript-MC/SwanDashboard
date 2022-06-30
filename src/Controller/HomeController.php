<?php

namespace App\Controller;

use App\Service\ModuleService;
use App\Service\StatisticsService;
use App\Service\UptimeService;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function root(): Response
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->render('public.html.twig');

        return $this->redirectToRoute('home_availability');
    }

    #[Route('/home/availability', name: 'home_availability')]
    #[IsGranted('ROLE_USER')]
    public function availability(UptimeService $uptimeService, ModuleService $moduleService): Response
    {
        $uptime = $uptimeService->getUptime();
        return $this->render('home/availability.html.twig', [
            'uptimeDays' => $uptime[0],
            'uptimePercentage' => $uptime[1],
            'isOnline' => $uptimeService->isOnline(),
            'disabledModules' => $moduleService->getDisabledModules(),
        ]);
    }

    #[Route('/home/statistics', name: 'home_statistics')]
    #[IsGranted('ROLE_USER')]
    public function stats(StatisticsService $statsService): Response
    {
        return $this->render('home/statistics.html.twig', [
            'commandStats' => $statsService->getCommandStats()
        ]);
    }
}
