<?php

namespace App\Repository;

use App\Document\DiscordUser;
use App\Document\MessageLog;
use App\Entity\LogSearch;
use App\Utils\DiscordUtils;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use MongoDB\BSON\Regex;

class MessageLogRepository extends DocumentRepository
{

    public function findOneByMessageId(string $messageId): MessageLog
    {
        return $this->findOneBy(['messageId' => $messageId]);
    }

    public function findEditedMessagesOfChannel(string $channelId): Builder
    {
        return $this->createQueryBuilder()
            ->field('channelId')->equals($channelId)
            ->field('newContent')->notEqual(null)
            ->sort('messageId', 'DESC');
    }

    public function findDeletedMessagesOfChannel(string $channelId): Builder
    {
        return $this->createQueryBuilder()
            ->field('channelId')->equals($channelId)
            ->field('newContent')->equals(null)
            ->sort('messageId', 'DESC');
    }

    /**
     * @param LogSearch $logSearch
     * @return Builder
     */
    public function search(LogSearch $logSearch): Builder
    {
        $query = $this->createQueryBuilder();
        if ($logSearch->getUserId()) {
            $userId = $this->getDocumentManager()
                ->getRepository(DiscordUser::class)
                ->findOneBy(['userId' => $logSearch->getUserId()]);
            if ($userId)
                $query->field('user')->equals($userId->getId());
        }
        if ($logSearch->getChannelId())
            $query->field('channelId')->equals($logSearch->getChannelId());
        if ($logSearch->getAfterDate())
            $query->field('messageId')->gte((DiscordUtils::getSnowflakeFromTimestamp($logSearch->getAfterDate())));
        if ($logSearch->getBeforeDate())
            $query->field('messageId')->lte((DiscordUtils::getSnowflakeFromTimestamp($logSearch->getBeforeDate())));
        if ($logSearch->getMessageId())
            $query->field('messageId')->equals($logSearch->getMessageId());
        if ($logSearch->getOldContent())
            $query->field('oldContent')->equals(new Regex('.*' . $logSearch->getOldContent() . '.*', 'i'));
        if ($logSearch->getNewContent())
            $query->field('newContent')->equals(new Regex('.*' . $logSearch->getNewContent() . '.*', 'i'));
        return $query;
    }

    public function searchEditedMessages(LogSearch $logSearch): Builder
    {
        $query = $this->search($logSearch);
        return $query->field('newContent')->notEqual(null);
    }

    public function searchDeletedMessages(LogSearch $logSearch): Builder
    {
        $query = $this->search($logSearch);
        return $query->field('newContent')->equals(null);
    }

}
