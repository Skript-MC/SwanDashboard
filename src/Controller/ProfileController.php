<?php

namespace App\Controller;

use App\Document\DiscordUser;
use App\Service\DiscordService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    private DiscordService $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    #[Route('', name: 'profile')]
    public function home(): Response
    {
        /** @var DiscordUser $user */
        $user = $this->getUser();
        return $this->render('profile.html.twig', [
            'discordRoles' => $this->discordService->getRolesFromSnowflakes($user->getDiscordRoles())
        ]);
    }
}
