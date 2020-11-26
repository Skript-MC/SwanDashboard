<?php

namespace App\Controller;

use App\Document\Channel;
use App\Document\MessageHistory;
use App\Document\SharedConfig;
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

    /**
     * @Route("/", name="history")
     * @param DocumentManager $dm
     * @return Response
     */
    public function home(DocumentManager $dm): Response
    {
        return $this->render('history.html.twig', [
            'allChannels' => $this->getAllChannels($dm),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'messages' => [],
        ]);
    }

    private function getAllChannels(DocumentManager $dm): array
    {
        return $dm->getRepository(Channel::class)
            ->findAll();
    }

    private function getLoggedChannels(DocumentManager $dm): array
    {
        return $dm->getRepository(SharedConfig::class)
            ->findOneBy(['name' => 'archived-channels'])
            ->getValue();
    }

    /**
     * @Route("/{channelId}", name="history-channel")
     * @param int $channelId
     * @param DocumentManager $dm
     * @return Response
     */
    public function view(int $channelId, DocumentManager $dm): Response
    {
        $messages = $dm->getRepository(MessageHistory::class)->findBy(['channel.id' => $channelId]);
        return $this->render('history.html.twig', [
            'allChannels' => $this->getAllChannels($dm),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'messages' => $messages
        ]);
    }

}
