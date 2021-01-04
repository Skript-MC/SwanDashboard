<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DiscordLinkMessageExtension extends AbstractExtension
{
    private const DISCORD_GUILD = 533791418259341313;

    public function getFilters()
    {
        return [
            new TwigFilter('discordLink', [$this, 'discordLink']),
        ];
    }

    public function discordLink(int $channelId, int $messageId)
    {
        return "https://discord.com/channels/" . self::DISCORD_GUILD . "/" . $channelId . "/" . $messageId;
    }
}
