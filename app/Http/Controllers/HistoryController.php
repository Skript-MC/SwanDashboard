<?php

namespace App\Http\Controllers;

use App\Models\MessageHistory;
use App\Utils\DataProvider;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return View
     */
    public function index()
    {
        if (auth()->user()->rank < 3) abort(403);
        $log_channels = DataProvider::getConfig('log_channels');
        return view('history.index', [
            'channels' => $log_channels == null ? [] : $log_channels,
        ]);
    }

    /**
     * @param $channelName
     * @return View
     */
    public function channelHistory($channelName)
    {
        if (auth()->user()->rank < 3) abort(403);
        $channels = DataProvider::getConfig('log_channels');
        $messages = MessageHistory::query()
            ->where('channelName', '=', $channelName)
            ->paginate(DataProvider::getConfig('pagination'));
        return view('history.index', [
            'channels' => $channels == null ? [] : $channels,
            'messages' => $messages
        ]);
    }
}
