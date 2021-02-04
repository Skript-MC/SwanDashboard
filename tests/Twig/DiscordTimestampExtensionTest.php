<?php

namespace App\Tests\Twig;

use App\Twig\DiscordTimestampExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiscordTimestampExtensionTest extends WebTestCase
{

    /**
     * @dataProvider provideData
     * @param array $data
     */
    function testTimestamp(array $data): void
    {
        $string = (new DiscordTimestampExtension())->formatTimestamp($data[0]);
        $this->assertEquals($data[1], $string);
    }

    public function provideData(): array
    {
        return [
            [[511206529886715944, '11/11/2018 à 15:52']],
            [[593479708461760551, '26/06/2019 à 16:36']],
            [[714903497627402354, '26/05/2020 à 18:11']],
            [[806846866460377119, '04/02/2021 à 11:21']]
        ];
    }

}
