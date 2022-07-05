<?php

namespace App\Twig;

use App\Service\DiscordService;
use Psr\Cache\InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiscordUserExtension extends AbstractExtension
{
    private DiscordService $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('discordName', [$this, 'discordName']),
            new TwigFilter('discordUsername', [$this, 'discordUsername']),
            new TwigFilter('discordAvatar', [$this, 'discordAvatar']),
            new TwigFilter('discordDiscriminator', [$this, 'discordDiscriminator']),
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function discordName(int $discordId): string
    {
        $user = $this->discordService->getUser($discordId);
        return $user->username;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function discordUsername(int $discordId): string
    {
        $user = $this->discordService->getUser($discordId);
        return $user->username . '#' . $user->discriminator;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function discordAvatar(int $discordId): string
    {
        $user = $this->discordService->getUser($discordId);
        return 'https://cdn.discordapp.com/avatars/' . $user->id . '/' . $user->avatar . '.png';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function discordDiscriminator(int $discordId): string
    {
        $user = $this->discordService->getUser($discordId);
        return $user->discriminator;
    }
}
