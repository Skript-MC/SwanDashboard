<?php

namespace App\Service;

class GitVersion
{
    public static function getCommitHash(): ?string
    {
        $filePath = __DIR__ . '/../../.git/refs/heads/main';
        if (file_exists($filePath))
            return file_get_contents($filePath);
        return null; // @codeCoverageIgnore
    }
}
