<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiscordTimestampExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('discordTimestamp', [$this, 'formatTimestamp']),
        ];
    }

    public function formatTimestamp($snowflake)
    {
        $time = (string) ($snowflake >> 22);
        return date('d/m/yy à h:m A', (((int) substr($time, 0, -3)) + 1420070400).'.'.substr($time, -3));
    }
}
