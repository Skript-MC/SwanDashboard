<?php

namespace App\Controller;

use App\Service\CacheService;
use App\Service\DiscordService;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('', name: 'home')]
    public function home(DiscordService $discordService, CacheService $cacheService): Response
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->render('welcome.html.twig');
        $guild = $discordService->getGuild();
        return $this->render('dashboard.html.twig', [
            'discordMembers' => $guild?->approximate_member_count ?? 'Inconnu',
            'discordOnlineMembers' => $guild?->approximate_presence_count ?? 'Inconnu',
            'commandStats' => $cacheService->getCommandStats()
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('discord')
            ->redirect(['identify'], []);
    }

    #[Route('/error', name: 'error')]
    public function error(): Response
    {
        return $this->render('error.html.twig', [
            'title' => 'Une erreur est survenue',
            'fa' => 'exclamation-circle',
            'message' => 'Nous ne savons pas exactement ce qu\'il s\'est passé. Si le problème persiste, n\'hésitez pas à nous contacter sur Discord.'
        ]);
    }
    #[Route('/login/error', name: 'login_error')]
    public function login_error(): Response
    {
        return $this->render('error.html.twig', [
            'title' => 'Une erreur est survenue',
            'fa' => 'exclamation-circle',
            'message' => 'Nous n\'avons pas pu accéder à votre compte afin de vous authentifier. Si le problème persiste, n\'hésitez pas à nous contacter sur Discord.'
        ]);
    }
}
