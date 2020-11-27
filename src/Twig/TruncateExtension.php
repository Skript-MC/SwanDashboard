<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TruncateExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('truncate', [$this, 'truncateString']),
        ];
    }

    public function truncateString(string $string)
    {
        if (strlen($string) >= 100)
            return substr($string, 0, 97) . '...';
        return $string;
    }
}
