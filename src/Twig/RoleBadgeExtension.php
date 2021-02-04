<?php

namespace App\Twig;

use RestCord\Model\Permissions\Role;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RoleBadgeExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     * @codeCoverageIgnore
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('roleBadge', [$this, 'roleBadge']),
        ];
    }

    public function roleBadge(Role $discordRole): string
    {
        $color = '#' . dechex($discordRole->color);
        if ($color === '0' || !preg_match('/^#?(([a-f0-9]{3}){1,2})$/i', $color)) $color = null;
        return '<span class="badge mr-1 ml-1 badge-pill ' . ($color ? 'text-white' : 'text-black') . '" style="background-color: ' . ($color ?? '#00000') . '">' . strip_tags($discordRole->name) . '</span>';
    }
}
