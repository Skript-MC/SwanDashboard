<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function self() {
        return view('profile.self', [
            'user' => auth()->user()
        ]);
    }

}
