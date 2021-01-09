<?php

namespace App\Controller;

use App\Discord\SwanClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/roles")
 * @IsGranted("ROLE_ADMIN")
 */
class RoleController extends AbstractController
{

    /**
     * @Route("", name="roles")
     * @param SwanClient $swanClient
     * @return Response
     */
    public function home(SwanClient $swanClient): Response
    {
        return $this->render('roles/home.html.twig', [
            'discordRoles' => $swanClient->getRoles(),
            'symfonyRoles' => $this->getParameter('security.role_hierarchy.roles')
        ]);
    }

}
