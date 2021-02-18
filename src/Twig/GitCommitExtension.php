<?php

namespace App\Twig;

use App\Service\GitVersionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GitCommitExtension extends AbstractExtension
{
    const GITHUB_REPO = "https://github.com/Skript-MC/SwanDashboard/commit/";

    /**
     * @return TwigFunction[]
     * @codeCoverageIgnore
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('gitCommit', [$this, 'gitCommit']),
        ];
    }

    public function gitCommit(): string
    {
        $commitHash = GitVersionService::getCommitHash();
        $shortHash = substr($commitHash, 0, 6);
        return '<a href="' . self::GITHUB_REPO . $commitHash . '">' . $shortHash . '</a>';
    }
}
