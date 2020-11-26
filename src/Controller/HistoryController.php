<?php

namespace App\Controller;

use App\Document\Channel;
use App\Document\MessageHistory;
use App\Document\SharedConfig;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/history")
 * @IsGranted("ROLE_USER")
 */
class HistoryController extends AbstractController
{

    private function getLoggedChannels(DocumentManager $dm): array
    {
        return $dm->getRepository(SharedConfig::class)
            ->findOneBy(['name' => 'archived-channels'])
            ->getValue();
    }

    private function getAllChannels(DocumentManager $dm): array
    {
        return $dm->getRepository(Channel::class)
            ->findAll();
    }

    /**
     * @Route("/", name="history")
     * @param DocumentManager $dm
     * @return Response
     */
    public function home(DocumentManager $dm): Response
    {
        return $this->render('history.html.twig', [
            'allChannels' => $this->getAllChannels($dm),
            'loggedChannels' => $this->getLoggedChannels($dm)
        ]);
    }

    /**
     * @Route("/{channelId}", name="history-channel")
     * @param int $channelId
     * @param DocumentManager $dm
     * @return Response
     */
    public function view(int $channelId, DocumentManager $dm): Response
    {

        $messages = $dm->getRepository(MessageHistory::class)
            ->findBy(['channelId' => $channelId]);

        foreach ($messages as $message) {
            $user = $dm->getRepository(User::class)
                ->findOneBy(['discordId' => $message->getUserId()]);

            $message->setUserName($user->getUsername());
            $message->setUserAvatarUrl($user->getAvatarUrl());

            $message->setChannelName(
                $dm->getRepository(Channel::class)
                ->findOneBy(['channelId' => $message->getChannelId()])
            );
        }

        return $this->render('history.html.twig', [
            'allChannels' => $this->getAllChannels($dm),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'messages' => $messages
        ]);
    }

}
