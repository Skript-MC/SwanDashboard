<?php

namespace App\Discord;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class SwanClient implements ServiceSubscriberInterface
{

    const CACHE_TTL = 60 * 10; // 10 minutes

    private HttpClientInterface $httpClient;
    private ContainerBagInterface $containerBag;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $discord_client, ContainerBagInterface $containerBag, CacheInterface $swanCache)
    {
        $this->containerBag = $containerBag;
        $this->httpClient = $discord_client;
        $this->cache = $swanCache;
    }

    public static function arrayToRoles(array $roles): array
    {
        usort($roles, function (array $roleA, array $roleB): int {
            return $roleB['position'] - $roleA['position'];
        });
        return array_map(function (array $role): DiscordRole {
            return new DiscordRole($role);
        }, $roles);
    }

    public static function arrayToChannels(array $channels): array
    {
        $channels = array_map(function (array $channel): DiscordChannel {
            return new DiscordChannel($channel);
        }, $channels);
        $categories = [];
        foreach ($channels as $channel) {
            if ($channel->getType() == 4)
                $categories[$channel->getId()] = new DiscordCategory($channel->toArray());
        }
        foreach ($channels as $channel) {
            if ($channel->getType() == 0) {
                $categories[$channel->getParentId()]->addChannel($channel);
            }
        }
        return $categories;
    }

    public function getRolesFromSnowflakes(array $snowflakes): array
    {
        $roles = $this->getRoles(true);
        if (!isset($roles)) return [];
        return self::arrayToRoles(array_map(function (int $snowflake) use ($roles) {
            return $roles[array_search($snowflake, array_column($roles, 'id'))];
        }, $snowflakes));
    }

    public function getMember(int $userId): ?DiscordMember
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                '/api/v8/guilds/' . $this->containerBag->get('discordGuild') .  '/members/' . $userId
            );
            return new DiscordMember($response->toArray());
        } catch (RedirectionExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return null;
        }
    }

    public function getRoles(bool $raw = false): ?array
    {
        try {
            $roles = $this->cache->get('discordRoles', function (CacheItem $item) {
                $response = $this->httpClient->request(
                    'GET',
                    '/api/v8/guilds/' . $this->containerBag->get('discordGuild') . '/roles'
                );
                $item->expiresAfter(self::CACHE_TTL);
                return $response->toArray();
            });
            return ($raw) ? $roles : self::arrayToRoles($roles);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    public function getGuild(): ?DiscordGuild
    {
        try {
            $guild = $this->cache->get('discordGuild', function (CacheItem $item) {
                $response = $this->httpClient->request(
                    'GET',
                    '/api/v8/guilds/' . $this->containerBag->get('discordGuild') . '/preview'
                );
                $item->expiresAfter(self::CACHE_TTL);
                return $response->toArray();
            });
            return new DiscordGuild($guild);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    public function getChannels(bool $raw = false): ?array
    {
        try {
            return $this->cache->get('discordGuild', function (CacheItem $item) use ($raw) {
                $response = $this->httpClient->request(
                    'GET',
                    '/api/v8/guilds/' . $this->containerBag->get('discordGuild') . '/channels'
                );
                $item->expiresAfter(self::CACHE_TTL);
                return ($raw) ? $response->toArray() : self::arrayToChannels($response->toArray());
            });
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Returns an array of service types required by such instances, optionally keyed by the service names used internally.
     *
     * @return array The required service types, optionally keyed by service names
     */
    public static function getSubscribedServices(): array
    {
        return [
            'parameter_bag' => '?'.ContainerBagInterface::class
        ];
    }
}
