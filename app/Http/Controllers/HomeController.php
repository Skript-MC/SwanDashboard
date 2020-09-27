<?php


namespace App\Http\Controllers;


use App\Utils\DataProvider;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * Return view of the welcome page, or dashboard if user is authenticated.
     *
     * @return View
     */
    public function index()
    {
        if (!auth()->check())
            return view('welcome'); // The user isn't auth.
        return view('dashboard', [
            'sentry' => count(DataProvider::getSentryIssues()),
            'commands' => DataProvider::getCommandStats(),
        ]);
    }

}
