<?php

namespace App\Controller;

use App\Document\MessageHistory;
use App\Document\SharedConfig;
use App\Document\User;
use App\Entity\HistoryQuery;
use App\Service\DiscordService;
use App\Utils\DiscordUtils;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Knp\Component\Pager\PaginatorInterface;
use MongoDB\BSON\Regex;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/history')]
#[IsGranted('ROLE_STAFF')]
class HistoryController extends AbstractController
{

    #[Route('', name: 'history')]
    public function home(DocumentManager $dm, DiscordService $discordService): Response
    {
        return $this->render('history/home.html.twig', [
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm)
        ]);
    }

    private function getLoggedChannels(DocumentManager $dm): array
    {
        $channels = $dm->getRepository(SharedConfig::class)
            ->findOneBy(['name' => 'archived-channels']);
        return isset($channels) ? $channels->getValue() : [];
    }

    #[Route('/channel/{channelId}', name: 'history:channel')]
    public function viewChannel(int $channelId, DocumentManager $dm, PaginatorInterface $paginator, Request $request, DiscordService $swanClient): Response
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
            'channels' => $swanClient->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'deletions' => $deletions,
            'editions' => $editions
        ]);
    }

    #[Route('/message/{messageId}', name: 'history:message')]
    public function viewMessage(int $messageId, DocumentManager $dm, DiscordService $discordService): Response
    {
        $message = $dm->getRepository(MessageHistory::class)
            ->findOneBy(['messageId' => $messageId]);

        if (!$message)
            return new RedirectResponse(
                $this->generateUrl('history'),
                Response::HTTP_SEE_OTHER
            );

        return $this->render('history/message.html.twig', [
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'message' => $message
        ]);
    }

    #[Route('/search', name: 'history:search')]
    public function searchMessages(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        $form = $this->createFormBuilder(new HistoryQuery())
            ->setMethod('GET')
            ->add('userId', IntegerType::class, [
                'required' => false
            ])
            ->add('beforeDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('afterDate', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'widget' => 'single_text'
            ])
            ->add('messageId', IntegerType::class, [
                'required' => false
            ])
            ->add('oldContent', TextType::class, [
                'required' => false
            ])
            ->add('newContent', TextType::class, [
                'required' => false
            ])
            ->add('channelId', IntegerType::class, [
                'required' => false
            ])
            ->getForm();
        $form->handleRequest($request);

        $query = $dm->createQueryBuilder(MessageHistory::class)
            ->find()
            ->sort('_id', 'DESC');

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var HistoryQuery $data */
            $data = $form->getData();
            if ($data->getUserId()) {
                $userId = $dm->getRepository(User::class)->findOneBy(['discordId' => $data->getUserId()]);
                $query->field('user')->equals($userId->getId());
            }
            if ($data->getAfterDate()) $query->field('messageId')->gte((DiscordUtils::getSnowflakeFromTimestamp($data->getAfterDate())));
            if ($data->getBeforeDate()) $query->field('messageId')->lte((DiscordUtils::getSnowflakeFromTimestamp($data->getBeforeDate())));
            if ($data->getMessageId()) $query->field('messageId')->equals($data->getMessageId());
            if ($data->getOldContent()) $query->field('oldContent')->equals(new Regex('.*' . $data->getOldContent() . '.*', 'i'));
            if ($data->getNewContent()) $query->field('newContent')->equals(new Regex('.*' . $data->getNewContent() . '.*', 'i'));
        }

        $editions = $paginator->paginate(
            $query->field('newContent')->notEqual(null)->getQuery(),
            $request->query->getInt('page', 1)
        );

        $deletions = $paginator->paginate(
            $query->field('newContent')->equals(null)->getQuery(),
            $request->query->getInt('page', 1)
        );

        return $this->render('history/search.html.twig', [
            'editions' => $editions,
            'deletions' => $deletions,
            'searchForm' => $form->createView()
        ]);

    }

    #[Route('/api/channels', methods: ['POST'])]
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
            // @codeCoverageIgnoreStart
        } catch (MongoDBException) {
            return new JsonResponse(['error' => 'Une erreur interne est survenue.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // @codeCoverageIgnoreEnd

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);

    }

}
