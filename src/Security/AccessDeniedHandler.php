<?php

namespace App\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler extends AbstractController implements AccessDeniedHandlerInterface
{
    /**
     * Handles an access denied failure.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     * @return Response
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        return $this->render('error.html.twig', [
            'title' => 'Accès non autorisé',
            'fa' => 'user-slash',
            'message' => 'Vous n\'avez pas accès à cette ressource.',
            'referer' => $request->headers->get('referer')
        ]);
    }
}
