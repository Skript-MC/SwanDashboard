<?php

namespace App\Controller;

use App\Document\User;
use App\Service\DiscordService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("", name="profile")
     * @param DiscordService $discordService
     * @return Response
     */
    public function home(DiscordService $discordService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('profile.html.twig', [
            'discordRoles' => $discordService->getRolesFromSnowflakes($user->getDiscordRoles())
        ]);
    }

    /**
     * @Route("/delete", name="profile:delete", methods={"POST"})
     * @param DocumentManager $dm
     * @return Response
     * @throws MongoDBException
     */
    public function deleteAccount(DocumentManager $dm): Response
    {
        if ($this->isGranted("ROLE_ADMIN")) {
            $this->addFlash('error', 'Les administrateurs ne peuvent pas supprimer leurs comptes.');
            return $this->redirectToRoute('profile');
        }
        $collection = $dm->getDocumentCollection(User::class);
        /** @var $user User */
        $user = $this->getUser();
        $collection->deleteOne(['_id' => $user->getId()]);
        return new RedirectResponse($this->generateUrl('home'));
    }

}
