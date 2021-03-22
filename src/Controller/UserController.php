<?php

namespace App\Controller;

use App\Repository\DiscordUserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: 'users')]
    public function home(Request $request, PaginatorInterface $paginator, DiscordUserRepository $discordUserRepository): Response
    {
        return $this->render('users/home.html.twig', [
            'users' => $paginator->paginate(
                $discordUserRepository->findAll(),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/{userId}', name: 'users:view', methods: ['GET'])]
    public function viewUser(string $userId, DiscordUserRepository $discordUserRepository): Response
    {
        $user = $discordUserRepository->findOneById($userId);
        if (!$user)
            return new RedirectResponse($this->generateUrl('users'));
        return $this->render('users/view.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{userId}', name: 'users:edit', methods: ['POST'])]
    public function editUser(string $userId, Request $request, DiscordUserRepository $discordUserRepository): Response
    {
        $username = $request->request->get('discordUsername');
        $avatarUrl = $request->request->get('discordAvatar');
        $dashboardRole = $request->request->get('dashboardRole');
        $targetUser = $discordUserRepository->findOneById($userId);
        if (!$username || !$avatarUrl || !$dashboardRole || !$targetUser || !in_array($dashboardRole, ['ROLE_USER', 'ROLE_STAFF', 'ROLE_ADMIN'])) {
            $this->addFlash('error', 'Certains champs sont vides ou incorrects.');
            return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
        }
        $targetUser->setUsername($username);
        $targetUser->setAvatarUrl($avatarUrl);
        $targetUser->setRoles([$dashboardRole]);
        $discordUserRepository->updateUser($targetUser);
        $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
        return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
    }
}
