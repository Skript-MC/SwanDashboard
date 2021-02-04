<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TruncateExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     * @codeCoverageIgnore
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncateString']),
        ];
    }

    public function truncateString(string $string, int $position = 100): string
    {
        if (strlen($string) >= $position)
            return substr($string, 0, $position - 3) . '...';
        return $string;
    }
}
