<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        return Socialite::driver('discord')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return RedirectResponse
     */
    public function callback()
    {
        $user = Socialite::driver('discord')->user();

        /** @var $existingUser User */
        $existingUser = User::query()->where('id', '=', $user->getId())->first();
        if ($existingUser) {
            $user = $existingUser;
        } else {
            $newUser = new User();
            $newUser->id = $user->getId();
            $newUser->nickname = $user->getNickname();
            $newUser->name = $user->getName();
            $newUser->avatar = $user->getAvatar();
            $newUser->rank = $user->getId() === '191495299884122112' ? 4 : 1;
            $newUser->save();
            $user = $newUser;
        }
        auth()->login($user, true);
        return redirect()->route('home');
    }

    /**
     * Logout user then redirect to home page.
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        auth()->logout();
        return redirect()->route('home');
    }

}
