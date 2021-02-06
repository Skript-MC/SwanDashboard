<?php

namespace App\Tests\Utils;

use App\Utils\DiscordUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiscordUtilsTest extends WebTestCase
{

    /**
     * @dataProvider provideData
     * @param array $data
     */
    function testSnowflakeFromTimestamp(array $data): void
    {
        $snowflake = DiscordUtils::getSnowflakeFromTimestamp($data[0]);
        $this->assertEquals($data[1], $snowflake);
    }

    function provideData(): array
    {
        return [
            [[1612606492225, 807554901763686400]],
            [[1612606520376, 807555019837538304]],
            [[1612606584278, 807555287861952512]]
        ];
    }

}
