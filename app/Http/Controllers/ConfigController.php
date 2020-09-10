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
        return view('config.dashboard', [
            'pagination' => DataProvider::getConfig('pagination')
        ]);
    }

    public function postDashboard(Request $request)
    {
        if (auth()->user()->rank < 3) abort(403);
        $configName = $request->post('name');
        Cache::forget($configName); // Clear the cache
        $config = Config::query()->firstWhere('name', '=', $configName);
        $config->value = (int)$request->post($configName);
        $config->save();
        return redirect()->back()->with($configName, 'Vos modifications ont été enregistrées.');
    }

}
