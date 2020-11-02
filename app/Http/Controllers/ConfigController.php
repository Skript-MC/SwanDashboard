<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Utils\DataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        if (auth()->user()->rank < 3) abort(403);
        $log_channels = DataProvider::getConfig('log_channels');
        return view('admin.config', [
            'pagination' => DataProvider::getConfig('pagination'),
            'log_channels' => $log_channels == null ? [] : $log_channels,
        ]);
    }

    public function postDashboard(Request $request)
    {
        if (auth()->user()->rank < 3) abort(403);
        $configName = $request->post('name');
        Cache::forget('config-' . $configName); // Clear the cache
        $config = Config::query()->firstWhere('name', '=', $configName);
        if (!$config) {
            $config = new Config();
            $config->name = $configName;
        }
        $config->value = ($configName === "pagination")
            ? (int) $request->post($configName)
            : $request->post($configName);
        $config->save();
        return redirect()->back()->with($configName, 'Vos modifications ont été enregistrées.');
    }

}
