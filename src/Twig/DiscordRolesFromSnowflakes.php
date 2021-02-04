<?php

namespace App\Twig;

use App\Service\DiscordService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class DiscordRolesFromSnowflakes
 * @package App\Twig
 * @codeCoverageIgnore This extension requires Discord API.
 */
class DiscordRolesFromSnowflakes extends AbstractExtension
{
    private DiscordService $discordService;

    public function __construct(DiscordService $discordService)
    {
        $this->discordService = $discordService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('discordRolesFromSnowflakes', [$this, 'discordRolesFromSnowflakes']),
        ];
    }

    public function discordRolesFromSnowflakes(array $snowflakes): array
    {
        return $this->discordService->getRolesFromSnowflakes($snowflakes);
    }
}
