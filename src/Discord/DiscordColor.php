<?php

namespace App\Discord;

class DiscordColor
{
    const DEFAULT = 4359924;
    const SUCCESS = 2082338;
    const PURPLE = 8454379;
    const DANGER = 13382400;
    const WARN = 16769536;

    public static function hexadecimalToDecimal(string $hexadecimalString): int
    {
        return hexdec($hexadecimalString) ?? self::DEFAULT;
    }

}
