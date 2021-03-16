<?php

namespace App\Controller;

use App\Document\DiscordUser;
use App\Repository\DiscordUserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
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
    private DiscordUserRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(DocumentManager $dm, PaginatorInterface $paginator)
    {
        $this->repository = $dm->getRepository(DiscordUser::class);
        $this->paginator = $paginator;
    }

    #[Route('', name: 'users')]
    public function home(Request $request): Response
    {
        return $this->render('users/home.html.twig', [
            'users' => $this->paginator->paginate(
                $this->repository->findAll(),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/{userId}', name: 'users:view', methods: ['GET'])]
    public function viewUser(string $userId): Response
    {
        $user = $this->repository->findOneById($userId);
        if (!$user)
            return new RedirectResponse($this->generateUrl('users'));
        return $this->render('users/view.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/{userId}', name: 'users:edit', methods: ['POST'])]
    public function editUser(string $userId, Request $request): Response
    {
        $username = $request->request->get('discordUsername');
        $avatarUrl = $request->request->get('discordAvatar');
        $dashboardRole = $request->request->get('dashboardRole');
        $targetUser = $this->repository->findOneById($userId);
        if (!$username || !$avatarUrl || !$dashboardRole || !$targetUser || !in_array($dashboardRole, ['ROLE_USER', 'ROLE_STAFF', 'ROLE_ADMIN'])) {
            $this->addFlash('error', 'Certains champs sont vides ou incorrects.');
            return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
        }
        $targetUser->setUsername($username);
        $targetUser->setAvatarUrl($avatarUrl);
        $targetUser->setRoles([$dashboardRole]);
        $this->repository->updateUser($targetUser);
        $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
        return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
    }
}
