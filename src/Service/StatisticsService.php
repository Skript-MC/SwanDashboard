<?php

namespace App\Service;

use App\Repository\CommandStatRepository;
use Symfony\Contracts\Cache\CacheInterface;

class StatisticsService
{

    private CommandStatRepository $commandStatRepository;
    private CacheInterface $cache;

    public function __construct(CommandStatRepository $commandStatRepository, CacheInterface $cache)
    {
        $this->commandStatRepository = $commandStatRepository;
        $this->cache = $cache;
    }

    public function getCommandStats(): array
    {
        return $this->cache->get('commandStats', function () {
            $commandStats = $this->commandStatRepository->getSorted();
            $commandNames = [];
            $commandValues = [];

            foreach ($commandStats as $commandStat) {
                $commandNames[] = $commandStat->getCommandId();
                $commandValues[] = $commandStat->getUses();
            }

            return [$commandNames, $commandValues];
        });
    }

}
