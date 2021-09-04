<?php

namespace App\Controller;

use App\Entity\LogSearch;
use App\Form\LogSearchType;
use App\Repository\MessageLogRepository;
use App\Repository\SwanChannelRepository;
use App\Service\DiscordService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/logs')]
#[IsGranted('ROLE_STAFF')]
class LogController extends AbstractController
{

    private SwanChannelRepository $swanChannelRepository;

    public function __construct(SwanChannelRepository $swanChannelRepository)
    {
        $this->swanChannelRepository = $swanChannelRepository;
    }

    #[Route('', name: 'logs')]
    public function home(): Response
    {
        return $this->render('logs/home.html.twig', [
            'channels' => $this->swanChannelRepository->findAll(),
            'loggedChannels' => $this->swanChannelRepository->findLoggedChannels()
        ]);
    }

    #[Route('/channel/{channelId}', name: 'logs:channel')]
    public function viewChannel(string $channelId, Request $request, PaginatorInterface $paginator, MessageLogRepository $messageLogRepository, DiscordService $discordService): Response
    {
        $deletions = $paginator->paginate(
            $messageLogRepository->findDeletedMessagesOfChannel($channelId),
            $request->query->getInt('pageDeletions', 1),
            10,
            ['pageParameterName' => 'pageDeletions']
        );
        $editions = $paginator->paginate(
            $messageLogRepository->findEditedMessagesOfChannel($channelId),
            $request->query->getInt('pageEditions', 1),
            10,
            ['pageParameterName' => 'pageDeletions']
        );
        return $this->render('logs/channel.html.twig', [
            'channels' => $this->swanChannelRepository->findAll(),
            'loggedChannels' => $this->swanChannelRepository->findLoggedChannels(),
            'deletions' => $deletions,
            'editions' => $editions
        ]);
    }

    #[Route('/message/{messageId}', name: 'logs:message')]
    public function viewMessage(string $messageId, MessageLogRepository $messageLogRepository, DiscordService $discordService): Response
    {
        $message = $messageLogRepository->findOneByMessageId($messageId);
        if (!$message)
            return new RedirectResponse($this->generateUrl('logs'), Response::HTTP_SEE_OTHER);
        return $this->render('logs/message.html.twig', [
            'channels' => $this->swanChannelRepository->findAll(),
            'loggedChannels' => $this->swanChannelRepository->findLoggedChannels(),
            'message' => $message
        ]);
    }

    #[Route('/search', name: 'logs:search')]
    public function searchMessages(Request $request, PaginatorInterface $paginator, MessageLogRepository $messageLogRepository): Response
    {
        $search = new LogSearch();
        $form = $this->createForm(LogSearchType::class, $search)
            ->handleRequest($request);
        $editions = $paginator->paginate(
            $messageLogRepository->searchEditedMessages($search),
            $request->query->getInt('page', 1),
            10,
            ['pageParameterName' => 'pageEditions']
        );
        $deletions = $paginator->paginate(
            $messageLogRepository->searchDeletedMessages($search),
            $request->query->getInt('page', 1),
            10,
            ['pageParameterName' => 'pageDeletions']
        );
        return $this->render('logs/search.html.twig', [
            'editions' => $editions,
            'deletions' => $deletions,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route('/api/channels', methods: ['POST'])]
    public function changeLoggingState(Request $request): JsonResponse
    {
        $channelId = $request->request->getAlnum('channelId');
        $checked = $request->request->getBoolean('checked');
        if (!$channelId || !isset($checked))
            return new JsonResponse(['error' => 'Votre requête est invalide.'], Response::HTTP_BAD_REQUEST);
        $channel = $this->swanChannelRepository->findChannelById($channelId);
        $channel->setLogged($checked);
        $this->swanChannelRepository->getDocumentManager()->persist($channel);
        $this->swanChannelRepository->getDocumentManager()->flush();
        return new JsonResponse(['status' => 'OK'], Response::HTTP_OK);
    }

}
