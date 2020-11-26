<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        if (!$this->isGranted('ROLE_USER'))
            return $this->render('welcome.html.twig');
        return $this->render('dashboard.html.twig');
    }

    /**
     * @Route("/login")
     * @param ClientRegistry $registry
     * @return Response
     */
    public function login(ClientRegistry $registry): Response
    {
        return $registry
            ->getClient('discord')
            ->redirect(['identify'], [/* Empty */]);
    }

}
