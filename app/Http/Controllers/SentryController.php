<?php

namespace App\Http\Controllers;

use App\Utils\DataProvider;

class SentryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->rank < 4) abort(403);
        return view('sentry.index', [
            'reports' => DataProvider::getSentryIssues()
        ]);
    }

}
