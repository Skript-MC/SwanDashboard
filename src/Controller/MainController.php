<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function main(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/auth")
     * @param ClientRegistry $registry
     * @return Response
     */
    public function login(ClientRegistry $registry): Response
    {
        return $registry
            ->getClient('discord')
            ->redirect(['identify'], [ /* Empty */ ]);
    }

}