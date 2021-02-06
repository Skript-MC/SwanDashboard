<?php

namespace App\Service;

use GuzzleHttp\Command\Exception\CommandClientException;
use Psr\Cache\InvalidArgumentException;
use RestCord\DiscordClient;
use RestCord\Model\Guild\Guild;
use RestCord\Model\Guild\GuildMember;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class DiscordService
 * @package App\Service
 */
class DiscordService implements ServiceSubscriberInterface
{

    const CACHE_TTL = 60 * 10; // 10 minutes

    private DiscordClient $discordClient;
    private int $discordGuild;
    private CacheInterface $cache;

    public function __construct(ContainerBagInterface $containerBag, CacheInterface $swanCache)
    {
        $this->discordClient = new DiscordClient(['token' => $containerBag->get('discordSwanToken')]);
        $discordGuild = (int) $containerBag->get('discordGuild');
        $this->discordGuild = $discordGuild;
        $this->cache = $swanCache;
    }

    /**
     * Returns an array of service types required by such instances, optionally keyed by the service names used internally.
     *
     * @return array The required service types, optionally keyed by service names
     */
    public static function getSubscribedServices(): array
    {
        return [
            'parameter_bag' => '?' . ContainerBagInterface::class
        ];
    }

    public function getRolesFromSnowflakes(array $snowflakes): array
    {
        $roles = $this->getRoles();
        if (!isset($roles)) return [];
        return array_map(function (int $snowflake) use ($roles) {
            return $roles[array_search($snowflake, array_column($roles, 'id'))]; // @codeCoverageIgnore
        }, $snowflakes);
    }

    public function getRoles(): array
    {
        try {
            return $this->cache->get('discordRoles', function (CacheItem $item) {
                $item->expiresAfter(self::CACHE_TTL);
                return $this->discordClient->guild->getGuildRoles(['guild.id' => $this->discordGuild]);
            });
        } catch (InvalidArgumentException | CommandClientException) {
            return [];
        }
    }

    /**
     * @param int $userId
     * @return GuildMember|null
     * @codeCoverageIgnore This method will never be called since authentication is not unit tested.
     */
    public function getMember(int $userId): ?GuildMember
    {
        return $this->discordClient->guild->getGuildMember(['guild.id' => $this->discordGuild, 'user.id' => $userId]);
    }

    public function getChannels(): array
    {
        try {
            return $this->cache->get('discordChannels', function (CacheItem $item) {
                $item->expiresAfter(self::CACHE_TTL);
                $discordChannels = $this->discordClient->guild->getGuildChannels(['guild.id' => $this->discordGuild]);
                // @codeCoverageIgnoreStart
                $categories = [];
                $channels = [];
                foreach ($discordChannels as $channel)
                    if ($channel->type == 4) $categories[] = $channel;
                foreach ($discordChannels as $channel)
                    if ($channel->type == 0) $channels[$channel->parent_id][] = $channel;
                return [$categories, $channels];
                // @codeCoverageIgnoreStop
            });
        } catch (InvalidArgumentException | CommandClientException) {
            return [];
        }
    }

    public function getGuild(): ?Guild
    {
        try {
            return $this->cache->get('discordGuild', function (CacheItem $item) {
                $item->expiresAfter(self::CACHE_TTL);
                return $this->discordClient->guild->getGuild(['guild.id' => $this->discordGuild]);
            });
        } catch (InvalidArgumentException | CommandClientException) {
            return null;
        }
    }

}
