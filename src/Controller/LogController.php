<?php

namespace App\Controller;

use App\Document\MessageLog;
use App\Document\SharedConfig;
use App\Document\DiscordUser;
use App\Entity\LogQuery;
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

#[Route('/logs')]
#[IsGranted('ROLE_STAFF')]
class LogController extends AbstractController
{

    #[Route('', name: 'logs')]
    public function home(DocumentManager $dm, DiscordService $discordService): Response
    {
        return $this->render('logs/home.html.twig', [
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm)
        ]);
    }

    private function getLoggedChannels(DocumentManager $dm): array
    {
        $channels = $dm->getRepository(SharedConfig::class)
            ->findOneBy(['name' => 'logged-channels']);
        return isset($channels) ? $channels->getValue() : [];
    }

    #[Route('/channel/{channelId}', name: 'logs:channel')]
    public function viewChannel(int $channelId, DocumentManager $dm, PaginatorInterface $paginator, Request $request, DiscordService $swanClient): Response
    {
        $deletions = $paginator->paginate(
            $dm->createQueryBuilder(MessageLog::class)
                ->field('channelId')->equals($channelId)
                ->field('newContent')->equals(null)
                ->sort('messageId', 'DESC')
                ->getQuery(),
            $request->query->getInt('pageDeletions', 1)
        );
        $deletions->setPaginatorOptions(['pageParameterName' => 'pageDeletions']);

        $editions = $paginator->paginate(
            $dm->createQueryBuilder(MessageLog::class)
                ->field('channelId')->equals($channelId)
                ->field('newContent')->notEqual(null)
                ->sort('messageId', 'DESC'),
            $request->query->getInt('pageEditions', 1)
        );
        $editions->setPaginatorOptions(['pageParameterName' => 'pageEditions']);

        return $this->render('logs/channel.html.twig', [
            'channels' => $swanClient->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'deletions' => $deletions,
            'editions' => $editions
        ]);
    }

    #[Route('/message/{messageId}', name: 'logs:message')]
    public function viewMessage(int $messageId, DocumentManager $dm, DiscordService $discordService): Response
    {
        $message = $dm->getRepository(MessageLog::class)
            ->findOneBy(['messageId' => $messageId]);

        if (!$message)
            return new RedirectResponse(
                $this->generateUrl('logs'),
                Response::HTTP_SEE_OTHER
            );

        return $this->render('logs/message.html.twig', [
            'channels' => $discordService->getChannels(),
            'loggedChannels' => $this->getLoggedChannels($dm),
            'message' => $message
        ]);
    }

    #[Route('/search', name: 'logs:search')]
    public function searchMessages(Request $request, DocumentManager $dm, PaginatorInterface $paginator): Response
    {
        $form = $this->createFormBuilder(new LogQuery())
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

        $query = $dm->createQueryBuilder(MessageLog::class)
            ->find()
            ->sort('_id', 'DESC');

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LogQuery $data */
            $data = $form->getData();
            if ($data->getUserId()) {
                $userId = $dm->getRepository(DiscordUser::class)->findOneBy(['userId' => $data->getUserId()]);
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

        return $this->render('logs/search.html.twig', [
            'editions' => $editions,
            'deletions' => $deletions,
            'searchForm' => $form->createView()
        ]);

    }

    #[Route('/api/channels', methods: ['POST'])]
    public function changeLoggingState(Request $request, DocumentManager $dm): JsonResponse
    {
        $channelId = $request->request->getAlnum('channelId');
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
                ->field('name')->equals('logged-channels')
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
