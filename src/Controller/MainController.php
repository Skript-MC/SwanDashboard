<?php

namespace App\Controller;

use App\Discord\SwanClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("", name="home")
     * @param SwanClient $swanClient
     * @return Response
     */
    public function home(SwanClient $swanClient): Response
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->render('welcome.html.twig');
        $guild = $swanClient->getGuild();
        return $this->render('dashboard.html.twig', [
            'discordMembers' => $guild->getMemberCount() ?? 'Inconnu',
            'discordOnlineMembers' => $guild->getPresenceCount() ?? 'Inconnu'
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param ClientRegistry $registry
     * @return Response
     */
    public function login(ClientRegistry $registry): Response
    {
        return $registry
            ->getClient('discord')
            ->redirect(['identify'], ['prompt' => 'none']);
    }

    /**
     * @Route("/error", name="error")
     * @param Request $request
     * @return Response
     */
    public function error(Request $request): Response
    {
        return $this->render('error.html.twig', [
            'title' => 'Une erreur est survenue',
            'fa' => 'exclamation-circle',
            'message' => ($request->get('message') ?? 'Nous ne savons pas exactement ce qu\'il s\'est passé.') . ' Si le problème persiste, n\'hésitez pas à nous contacter sur Discord.'
        ]);
    }

}
