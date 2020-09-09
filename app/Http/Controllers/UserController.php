<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\DataProvider;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->rank < 3) abort(403);
        return view('admin.users', [
            'users' => User::query()->paginate(DataProvider::getConfig('pagination'))
        ]);
    }

    public function edit($id)
    {
        if (auth()->user()->rank < 3) abort(403);
        $user = User::query()->firstWhere('id', '=', $id);
        if (!$user) return redirect()->route('users');
        return view('profile.edit', [
            'user' => $user
        ]);
    }

    public function postEdit(Request $request)
    {
        if (auth()->user()->rank < 3) abort(403);
        $user = User::query()->firstWhere('id', '=', $request->post('discordId'));
        $rank = (int)$request->post('dashRole');

        if (auth()->user()->rank < $rank) return redirect()->back()->with('error', 'Vous ne pouvez pas modifier un rôle supérieur au votre.');
        if (auth()->user()->rank < $user->rank) return redirect()->back()->with('error', 'Vous ne pouvez pas modifier cet utilisateur.');

        $user->nickname = $request->post('discordNickname');
        $user->name = $request->post('discordUsername');
        $user->avatar = $request->post('discordAvatar');
        $user->rank = $rank;
        $user->save();

        return redirect()->back()->with('success', 'Vos modifications ont été enregistrées.');
    }

}
