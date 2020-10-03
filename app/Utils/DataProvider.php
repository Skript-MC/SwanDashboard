<?php

namespace App\Utils;

use App\Models\CommandStats;
use App\Models\Config;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DataProvider
{

    /**
     * Recover cached Sentry data.
     * A new query will be performed if the cache has expired.
     *
     * @param bool $force
     * @return mixed
     */
    public static function getSentryIssues(bool $force = false)
    {
        if (!$force && Cache::has('sentry')) {
            return Cache::get('sentry');
        } else {
            $sentryData = [];
            try {
                $sentryData = json_decode(Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SENTRY_API_TOKEN')
                ])->get(env('SENTRY_API_URL') . '/issues/'), true);
            } catch (ConnectionException $e) { unset($e); } // Ignoring the exception
            if ($sentryData['detail'] ?? false) $sentryData = [];
            Cache::put('sentry', $sentryData, 600); // 10 minutes
            return $sentryData;
        }
    }

    /**
     * Recover cached config data by with their name.
     * A new query will be performed if the cache has expired.
     *
     * @param string $name
     * @param bool $force
     * @return mixed
     */
    public static function getConfig(string $name, bool $force = false)
    {
        if (!$force && Cache::has('config-' . $name)) {
            return Cache::get('config-' . $name);
        } else {
            $configData = Config::query()->firstWhere('name', '=', $name);
            if (!$configData) return null;
            Cache::put('config-' . $name, $configData->value);
            return $configData->value;
        }
    }

    /**
     * Recover cached command statistics.
     * A new query will be performed if the cache has expired.
     *
     * @param bool $force
     * @return mixed
     */
    public static function getCommandStats($force = false)
    {
        if (!$force && Cache::has('commandStats')) {
            return Cache::get('commandStats');
        } else {
            $commandStats = CommandStats::query()->orderByDesc('uses')->get();
            if (!$commandStats) return null;
            // This definitively needs a better way!
            // TODO: Improve the #getCommandStats' function
            $data = [];
            $data['names'] = [];
            $data['values'] = [];
            foreach ($commandStats as $command) {
                array_push($data['names'], $command['commandId']);
                array_push($data['values'], $command['uses']);
            }
            Cache::put('commandStats', $data);
            return $data;
        }
    }

}
