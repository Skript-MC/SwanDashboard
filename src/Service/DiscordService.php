<?php

namespace App\Service;

use App\Repository\SanctionRepository;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use RestCord\DiscordClient;
use RestCord\Model\User\User;
use Symfony\Contracts\Cache\CacheInterface;

class DiscordService
{

    private CacheInterface $cache;
    private DiscordClient $discordClient;
    private int $guildId;

    public function __construct(CacheInterface $cache, string $guildId, string $discordToken)
    {
        $this->cache = $cache;
        $this->discordClient = new DiscordClient(['token' => $discordToken]);
        $this->guildId = (int) $guildId;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getUser(int $userId): User
    {
        return $this->cache->get('discordService_user_' . $userId, function (CacheItemInterface $item) use ($userId) {
            // 60s * 60min * 24h * 30d = 30 days
            $item->expiresAfter(60 * 60 * 24 * 30);
            return $this->discordClient->user->getUser(['user.id' => $userId]);
        });
    }

}
