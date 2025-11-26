<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::firstOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'role' => 1
        ]);

        // Check if request wants JSON (API request)
        // $token = $user->createToken('authToken')->plainTextToken;
        // return response()->json(['token' => $token, 'user' => $user]);

        // Web authentication
        Auth::login($user);
        return redirect()->intended('/dashboard');
    }
}
