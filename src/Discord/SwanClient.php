<?php

namespace App\Discord;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class SwanClient implements ServiceSubscriberInterface
{

    private HttpClientInterface $httpClient;
    private ContainerBagInterface $containerBag;

    public function __construct(HttpClientInterface $discord_client, ContainerBagInterface $containerBag)
    {
        $this->containerBag = $containerBag;
        $this->httpClient = $discord_client;
    }

    public static function arrayToRoles(array $roles): array
    {
        usort($roles, function (array $roleA, array $roleB): int { return $roleB['position'] - $roleA['position']; });
        return array_map(function (array $role): DiscordRole  {return new DiscordRole($role); }, $roles);
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

    public function getRolesFromSnowflakes(array $snowflakes): array
    {
        $roles = $this->getRoles(true);
        return self::arrayToRoles(array_map(function (int $snowflake) use ($roles) {
            return $roles[array_search($snowflake, array_column($roles, 'id'))];
        }, $snowflakes));
    }

    public function getRoles(bool $raw = false): ?array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                '/api/v8/guilds/' . $this->containerBag->get('discordGuild') . '/roles'
            );
            $responseArray = $response->toArray();
            return ($raw) ? $responseArray : self::arrayToRoles($responseArray);
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            return null;
        }
    }

    public function getGuild(): ?DiscordGuild
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                '/api/v8/guilds/' . $this->containerBag->get('discordGuild') . '/preview'
            );
            return new DiscordGuild($response->toArray());
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
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
