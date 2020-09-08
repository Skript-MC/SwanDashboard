<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DataProvider
{

    public static function getSentryIssues()
    {
        if (Cache::has('sentry')) {
            return Cache::get('sentry');
        } else {
            $sentryData = json_decode(Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SENTRY_API_TOKEN')
            ])->get(env('SENTRY_API_URL') . '/issues/'), true);
            Cache::put('sentry', $sentryData, 600); // 10 minutes
            return $sentryData;
        }
    }

}
