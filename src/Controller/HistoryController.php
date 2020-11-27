<?php

namespace App\Controller;

use App\Document\Channel;
use App\Document\MessageHistory;
use App\Document\SharedConfig;
use Doctrine\ODM\MongoDB\DocumentManager;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function view(int $channelId, DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        $messages = $paginator->paginate(
            $dm->createQueryBuilder(MessageHistory::class)
                ->field('channel.id')->equals($channelId)
                ->sort('messageId', 'DESC')
                ->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('history.html.twig', [
            'allChannels' => $this->getAllChannels($dm),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'messages' => $messages
        ]);
    }

}
