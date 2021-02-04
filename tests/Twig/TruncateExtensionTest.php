<?php

namespace App\Tests\Twig;

use App\Twig\TruncateExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TruncateExtensionTest extends WebTestCase
{
    /**
     * @dataProvider provideData
     * @param array $data
     */
    function testTruncateString(array $data): void
    {
        $string = (new TruncateExtension())->truncateString($data[0], $data[1]);
        $this->assertEquals($data[2], $string);
    }

    public function provideData(): array
    {
        return [
            [['This is an example.', 20, 'This is an example.']],
            [['This is an example.', 19, 'This is an examp...']],
            [['Hello, how are you?', 8, 'Hello...']],
            [['   ', 0, '...']]
        ];
    }
}
