<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AuthTargetPathExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('targetPath', [$this, 'targetPath']),
        ];
    }

    public function targetPath(SessionInterface $session, string $firewallName)
    {
        return $session->get('_security.' . $firewallName . '.target_path');
    }
}
