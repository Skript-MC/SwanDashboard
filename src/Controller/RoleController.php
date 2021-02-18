<?php

namespace App\Controller;

use App\Service\DiscordService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/roles')]
#[IsGranted('ROLE_ADMIN')]
class RoleController extends AbstractController
{
    #[Route('', name: 'roles')]
    public function home(DiscordService $discordService): Response
    {
        return $this->render('roles/home.html.twig', [
            'discordRoles' => $discordService->getRoles(),
            'symfonyRoles' => $this->getParameter('security.role_hierarchy.roles') ?? []
        ]);
    }

}
