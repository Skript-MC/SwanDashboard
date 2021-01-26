<?php

namespace App\Service;

use App\Document\CommandStat;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class CacheService
{

    const CACHE_TTL = 60 * 10; // 10 minutes

    private CacheInterface $cache;
    private DocumentManager $dm;

    public function __construct(CacheInterface $swanCache, DocumentManager $dm)
    {
        $this->cache = $swanCache;
        $this->dm = $dm;
    }

    public function getCommandStats(): array
    {
        try {
            return $this->cache->get('commandStats', function (CacheItem $item) {
                $stats = $this->dm->createQueryBuilder(CommandStat::class)
                    ->find()
                    ->sort('uses', 'DESC')
                    ->getQuery()
                    ->execute()
                    ->toArray();

                $names = array_map(function (CommandStat $stat): string {
                    return $stat->getCommandId();
                }, $stats);

                $values = array_map(function (CommandStat $stat): int {
                    return $stat->getUses();
                }, $stats);

                $item->expiresAfter(self::CACHE_TTL);
                return [$names, $values];
            });
        } catch (InvalidArgumentException $e) {
            return [];
        }
    }

}
