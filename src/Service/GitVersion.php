<?php

namespace App\Service;

class GitVersion
{
    public static function getCommitHash(): string
    {
        return file_get_contents(__DIR__ . '/../../.git/refs/heads/master');
    }
}
