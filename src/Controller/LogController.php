<?php

namespace App\Controller;

use App\Entity\LogSearch;
use App\Form\LogSearchType;
use App\Repository\MessageLogRepository;
use App\Repository\SharedConfigRepository;
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

    private SharedConfigRepository $sharedConfigRepository;

    public function __construct(SharedConfigRepository $sharedConfigRepository)
    {
        $this->sharedConfigRepository = $sharedConfigRepository;
    }

    private function getLoggedChannels(): array
    {
        return $this->sharedConfigRepository->getLoggedChannels()?->getValue() ?? [];
    }

    #[Route('', name: 'logs')]
    public function home(DiscordService $discordService): Response
    {
        return $this->render('logs/home.html.twig', [
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels()
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
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels(),
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
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels(),
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
    public function changeLoggingState(Request $request, SharedConfigRepository $sharedConfigRepository): JsonResponse
    {
        $channelId = $request->request->getAlnum('channelId');
        $checked = $request->request->getBoolean('checked');
        if (!$channelId || !isset($checked))
            return new JsonResponse(['error' => 'Votre requête est invalide.'], Response::HTTP_BAD_REQUEST);
        $channels = $this->getLoggedChannels();
        if ($checked) { // Add the channel to the logged channels
            $channels[] = $channelId;
        } else { // Remove the channel from the logged channels
            $key = array_search($channelId, $channels);
            unset($channels[$key]);
        }
        // Update the logged channels with the updated array
        $sharedConfigRepository->updateLoggedChannels($channels);
        return new JsonResponse(['status' => 'OK'], Response::HTTP_OK);
    }

}
