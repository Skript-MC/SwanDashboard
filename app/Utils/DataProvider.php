<?php

namespace App\Utils;

use App\Models\Config;
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
            $sentryData = json_decode(Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SENTRY_API_TOKEN')
            ])->get(env('SENTRY_API_URL') . '/issues/'), true);
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
        if (!$force && Cache::has($name)) {
            return Cache::get($name);
        } else {
            $configData = Config::query()->firstWhere('name', '=', $name);
            if (!$configData) return null;
            Cache::put($name, $configData->value);
            return $configData->value;
        }
    }

}
