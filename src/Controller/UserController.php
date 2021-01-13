<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="users")
     * @param Request $request
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        return $this->render('users/home.html.twig', [
            'users' => $paginator->paginate(
                $dm->getRepository(User::class)->findAll(),
                $request->query->get('page')
            )
        ]);
    }

    /**
     * @Route("/{userId}", name="users:view", methods={"GET"})
     * @param int $userId
     * @param DocumentManager $dm
     * @return Response
     */
    public function viewUser(int $userId, DocumentManager $dm): Response
    {
        return $this->render('users/view.html.twig', [
            'user' => $dm->getRepository(User::class)->findOneBy(['_id' => $userId])
        ]);
    }

    /**
     * @Route("/{userId}", name="users:edit", methods={"POST"})
     * @param int $userId
     * @param Request $request
     * @return Response
     */
    public function editUser(int $userId, Request $request): Response
    {
        // TODO
    }

}
