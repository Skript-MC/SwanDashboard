<?php

namespace App\Controller\Knowledge;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/knowledge/messages')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'knowledge_messages')]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('knowledge_messages_list');
    }

    #[Route('/list', name: 'knowledge_messages_list')]
    public function list(): Response
    {
        return $this->render('knowledge/messages/list.html.twig');
    }

    #[Route('/new', name: 'knowledge_messages_new')]
    public function new(): Response
    {
        return $this->render('knowledge/messages/new.html.twig');
    }
}
