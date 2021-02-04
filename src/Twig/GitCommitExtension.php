<?php

namespace App\Twig;

use App\Service\GitVersion;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GitCommitExtension extends AbstractExtension
{
    const GITHUB_REPO = "https://github.com/Romitou/SwanDashboard/commit/";

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
        $commitHash = GitVersion::getCommitHash();
        $shortHash = substr($commitHash, 0, 6);
        return '<a href="' . self::GITHUB_REPO . $commitHash . '">' . $shortHash . '</a>';
    }
}
