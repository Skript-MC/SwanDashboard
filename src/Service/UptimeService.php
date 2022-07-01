<?php

namespace App\Service;

use App\Document\SwanUptime;
use App\Repository\SwanUptimeRepository;
use Symfony\Contracts\Cache\CacheInterface;

class UptimeService
{

    private SwanUptimeRepository $uptimeRepository;
    private CacheInterface $cache;

    public function __construct(SwanUptimeRepository $uptimeRepository, CacheInterface $cache)
    {
        $this->uptimeRepository = $uptimeRepository;
        $this->cache = $cache;
    }

    public function isOnline(): bool
    {
        $lastUptime = $this->uptimeRepository->uptimeOfToday();
        if (null === $lastUptime)
            return false;
        $uptimeHours = $lastUptime->getHours();
        if (empty($uptimeHours))
            return false;
        return (date('H') - 1) < end($uptimeHours);
    }

    public function getUptime(): array
    {
        $uptimes = $this->uptimeRepository->findAll();

        $uptimeDays = [];
        foreach ($uptimes as $uptime) {
            $uptimeDays[$uptime->getDay()] = $uptime;
        }

        $days = [];

        $end = date_create()->modify('+1 day');
        $begin = date_create()->modify('-90 days');

        $onlineHours = 0;
        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $dateString = $i->format("d-m-Y");
            if (array_key_exists($dateString, $uptimeDays)) {
                $onlineHours = $onlineHours + count($uptimeDays[$dateString]->getHours());
                $days[$dateString] = $uptimeDays[$dateString];
            } else {
                $days[$dateString] = (new SwanUptime())->setDay($dateString);
            }
        }
        $uptimePercentage = floor(($onlineHours / (count($days) * 24)) * 100);

        return [$days, $uptimePercentage];
    }


}
