<?php

namespace App\Tests\Twig;

use App\Twig\DiscordLinkMessageExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiscordLinkMessageExtensionTest extends WebTestCase
{
    /**
     * @dataProvider provideData
     * @param array $data
     */
    function testLinkMessage(array $data): void
    {
        $string = (new DiscordLinkMessageExtension())->discordLink($data[0], $data[1]);
        $this->assertEquals($data[2], $string);
    }

    public function provideData(): array
    {
        return [
            [[205024174799241217, 209959274799241274, 'https://discord.com/channels/533791418259341313/205024174799241217/209959274799241274']],
            [[296336338422364226, 408986471540899810, 'https://discord.com/channels/533791418259341313/296336338422364226/408986471540899810']],
            [[486938604384532043, 803516818636459964, 'https://discord.com/channels/533791418259341313/486938604384532043/803516818636459964']]
        ];
    }
}
