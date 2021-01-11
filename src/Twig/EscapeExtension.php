<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EscapeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('customEscape', [$this, 'customEscape']),
        ];
    }

    public function customEscape(string $input): string
    {
        $input = str_replace("\n", "\\n", $input);
        $input = str_replace("'", "\'", $input);
        return str_replace('\t', '\\n', $input);
    }
}
