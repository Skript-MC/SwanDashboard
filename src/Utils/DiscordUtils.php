<?php

namespace App\Utils;

class DiscordUtils
{
    // First second of 2015
    const DISCORD_EPOCH = 1420070400000;

    public static function getSnowflakeFromTimestamp(int $timestampMs): int
    {
        return ($timestampMs - self::DISCORD_EPOCH) << 22;
    }

}
