<?php

namespace App\Controller\Moderation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/moderation')]
class ModerationController extends AbstractController
{

    #[Route('/', name: 'moderation')]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('moderation_sanctions');
    }

}
