<?php

namespace App\Tests\Twig;

use App\Twig\RoleBadgeExtension;
use RestCord\Model\Permissions\Role;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoleBadgeExtensionTest extends WebTestCase
{

    /**
     * @dataProvider provideData
     * @param array $data
     */
    function testTruncateString(array $data): void
    {
        $discordRole = new Role([
            'id' => 'pineapple',
            'name' => $data[0],
            'color' => $data[1],
            'position' => 1
        ]);
        $string = (new RoleBadgeExtension())->roleBadge($discordRole);
        $this->assertEquals($data[2], $string);
    }

    public function provideData(): array
    {
        return [
            [['Pineapple', 3407689, '<span class="badge mr-1 ml-1 badge-pill text-white" style="background-color: #33ff49">Pineapple</span>']],
            [['Ananas', 15457076, '<span class="badge mr-1 ml-1 badge-pill text-white" style="background-color: #ebdb34">Ananas</span>']],
            [['Invalid color', 0, '<span class="badge mr-1 ml-1 badge-pill text-black" style="background-color: #00000">Invalid color</span>']]
        ];
    }

}
