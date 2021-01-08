<?php

namespace App\Discord;

use Psr\Container\ContainerInterface;
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
    private ContainerInterface $container;
    const DISCORD_API = "https://discord.com/api/v8";

    public function __construct(HttpClientInterface $httpClient, ContainerInterface $container)
    {
        $this->container = $container;
        $this->httpClient = $httpClient;
    }

    public function getMember(int $userId): ?DiscordMember
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::DISCORD_API . '/guilds/' . $this->container->get('parameter_bag')->get('discordGuild') .  '/members/' . $userId, [
                    'headers' => [
                        'Authorization' => 'Bot ' . $this->container->get('parameter_bag')->get('discordSwanToken')
                    ]
                ]
            );
            return new DiscordMember($response->toArray());
        } catch (RedirectionExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
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
