<?php

namespace App\Controller;

use App\Document\User;
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
    #[Route('', name: 'users')]
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('users/home.html.twig', [
            'users' => $paginator->paginate(
                $dm->getRepository(User::class)->findAll(),
                $request->query->getInt('page', 1)
            )
        ]);
    }

    #[Route('/{userId}', name: 'users:view', methods: ['GET'])]
    public function viewUser(int $userId, DocumentManager $dm): Response
    {
        return $this->render('users/view.html.twig', [
            'user' => $dm->getRepository(User::class)->findOneBy(['_id' => $userId])
        ]);
    }

    #[Route('/{userId}', name: 'users:edit', methods: ['POST'])]
    public function editUser(int $userId, Request $request, DocumentManager $dm): Response
    {
        $username = $request->request->get('discordUsername');
        $avatarUrl = $request->request->get('discordAvatar');
        $dashboardRole = $request->request->get('dashboardRole');
        $targetUser = $dm->getRepository(User::class)->findOneBy(['_id' => $userId]);
        if (!$username || !$avatarUrl || !$dashboardRole || !$targetUser || !in_array($dashboardRole, ['ROLE_USER', 'ROLE_STAFF', 'ROLE_ADMIN'])) {
            $this->addFlash('error', 'Certains champs sont vides ou incorrects.');
            return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
        }

        // For the moment, only admins can access to this page, so check permissions is useless.
        // if (!$this->isGranted($dashboardRole)) {
        //     $this->addFlash('error', 'Vous n\'avez pas la permission de modifier cet utilisateur.');
        //     return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
        // }

        $targetUser->setUsername($username);
        $targetUser->setAvatarUrl($avatarUrl);
        $targetUser->setDiscordRoles([$dashboardRole]);

        $dm->createQueryBuilder(User::class)
            ->updateOne()
            ->field('_id')->equals($userId)
            ->field('username')->set($username)
            ->field('avatarUrl')->set($avatarUrl)
            ->field('roles')->set([$dashboardRole])
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
        return new RedirectResponse($this->generateUrl('users:view', ['userId' => $userId]));
    }

}
