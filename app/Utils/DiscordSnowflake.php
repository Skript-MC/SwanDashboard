<?php

namespace App\Utils;

class DiscordSnowflake
{
    /**
     * Returns a timestamp for when a user's account was created.
     *
     * @param int $snowflake
     * @return float
     */
    public static function getTimestampOfSnowflake(int $snowflake): float
    {
        $time = (string) ($snowflake >> 22);
        return (float) ((((int) substr($time, 0, -3)) + 1420070400).'.'.substr($time, -3));
    }
}
