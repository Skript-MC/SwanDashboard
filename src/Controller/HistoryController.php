<?php

namespace App\Controller;

use App\Discord\SwanClient;
use App\Document\MessageHistory;
use App\Document\SharedConfig;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/history")
 * @IsGranted("ROLE_STAFF")
 */
class HistoryController extends AbstractController
{

    /**
     * @Route("", name="history")
     * @param DocumentManager $dm
     * @param SwanClient $swanClient
     * @return Response
     */
    public function home(DocumentManager $dm, SwanClient $swanClient): Response
    {
        return $this->render('history/home.html.twig', [
            'allChannels' => $swanClient->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm)
        ]);
    }

    private function getLoggedChannels(DocumentManager $dm): array
    {
        $channels = $dm->getRepository(SharedConfig::class)
            ->findOneBy(['name' => 'archived-channels']);
        return isset($channels) ? $channels->getValue() : [];
    }

    /**
     * @Route("/channel/{channelId}", name="history:channel")
     * @param int $channelId
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param SwanClient $swanClient
     * @return Response
     */
    public function viewChannel(int $channelId, DocumentManager $dm, PaginatorInterface $paginator, Request $request, SwanClient $swanClient): Response
    {
        $deletions = $paginator->paginate(
            $dm->createQueryBuilder(MessageHistory::class)
                ->field('channelId')->equals($channelId)
                ->field('newContent')->equals(null)
                ->sort('messageId', 'DESC')
                ->getQuery(),
            $request->query->getInt('pageDeletions', 1)
        );
        $deletions->setPaginatorOptions(['pageParameterName' => 'pageDeletions']);

        $editions = $paginator->paginate(
            $dm->createQueryBuilder(MessageHistory::class)
                ->field('channelId')->equals($channelId)
                ->field('newContent')->notEqual(null)
                ->sort('messageId', 'DESC'),
            $request->query->getInt('pageEditions', 1)
        );
        $editions->setPaginatorOptions(['pageParameterName' => 'pageEditions']);

        return $this->render('history/channel.html.twig', [
            'allChannels' => $swanClient->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'deletions' => $deletions,
            'editions' => $editions
        ]);
    }

    /**
     * @Route("/message/{messageId}", name="history:message")
     * @param int $messageId
     * @param DocumentManager $dm
     * @param SwanClient $swanClient
     * @return Response
     */
    public function viewMessage(int $messageId, DocumentManager $dm, SwanClient $swanClient): Response
    {
        $message = $dm->getRepository(MessageHistory::class)
            ->findOneBy(['messageId' => $messageId]);

        if (!$message)
            return new RedirectResponse(
                $this->generateUrl('history'),
                Response::HTTP_SEE_OTHER
            );

        return $this->render('history/message.html.twig', [
            'allChannels' => $swanClient->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'message' => $message
        ]);
    }

    /**
     * @Route("/search", methods={"POST"}, name="history:search")
     * @param Request $request
     * @return Response
     */
    public function searchMessages(Request $request): Response
    {
        $args = preg_split('/ /', $request->get('query'));

        $channel = null;
        $user = null;

        foreach ($args as $arg) {
            foreach (['channel', 'user'] as $item)
                if (str_contains($arg, $item . ':'))
                    $$item = preg_split('/' . $item . ':/', $arg)[1];
        }
        return new Response($channel . ' - ' . $user);
    }

    /**
     * @Route("/api/channels", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return JsonResponse
     */
    public function changeLoggingState(Request $request, DocumentManager $dm): JsonResponse
    {
        $channelId = $request->request->getInt('channelId');
        $checked = $request->request->getBoolean('checked');
        if (!$channelId || !isset($checked)) return new JsonResponse(['error' => 'Votre requête est invalide.'], Response::HTTP_BAD_REQUEST);

        $channels = $this->getLoggedChannels($dm);

        if ($checked) {
            $channels[] = $channelId;
        } else {
            $key = array_search($channelId, $channels);
            unset($channels[$key]);
        }

        try {
            $dm->createQueryBuilder(SharedConfig::class)
                ->findAndUpdate()
                ->field('name')->equals('archived-channels')
                ->field('value')->set($channels)
                ->getQuery()
                ->execute();
        } catch (MongoDBException $e) {
            return new JsonResponse(['error' => 'Une erreur interne est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);

    }

}
