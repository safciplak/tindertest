<?php

namespace App\Http\Controllers;

use App\Member;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Some methods which were generated with the app
    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        $authUser = $this->findOrCreateUser($user, $provider);
//        Auth::login($authUser, true);
        return redirect($this->redirectTo);
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        $authUser = Member::where('provider_id', $user->id)->first();
        if ($authUser) {
            return $authUser;
        }
        return Member::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'gender'    => $user->gender,
            'provider' => $provider,
            'provider_id' => $user->id
        ]);
    }
}
