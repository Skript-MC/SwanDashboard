<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiscordTimestampExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     * @codeCoverageIgnore
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('discordTimestamp', [$this, 'formatTimestamp']),
        ];
    }

    public function formatTimestamp(string $snowflake): string
    {
        $time = (string)($snowflake >> 22);
        return date('d/m/Y à H:i', (((int)substr($time, 0, -3)) + 1420070400) . '.' . substr($time, -3));
    }
}
