<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return view of the self profile of the current user.
     *
     * @return View
     */
    public function self() {
        return view('profile.self', [
            'user' => auth()->user()
        ]);
    }

}
