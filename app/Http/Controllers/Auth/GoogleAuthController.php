<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mentee;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $role = $request->query('role');

        if (in_array($role, ['mentor', 'mentee'], true)) {
            $request->session()->put('google_auth_role', $role);
        } else {
            $request->session()->forget('google_auth_role');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Connexion Google impossible. Veuillez reessayer.']);
        }

        if (! $googleUser->getEmail()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Votre compte Google ne fournit pas d adresse email.']);
        }

        $role = $request->session()->pull('google_auth_role');

        $user = DB::transaction(function () use ($googleUser, $role) {
            $user = User::query()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->forceFill([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();

                return $user;
            }

            if (! in_array($role, ['mentor', 'mentee'], true)) {
                return null;
            }

            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Utilisateur Google',
                'email' => $googleUser->getEmail(),
                'password' => Str::password(32),
                'role' => $role,
                'google_id' => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]);

            $this->createRoleProfile($user);

            event(new Registered($user));

            return $user;
        });

        if (! $user) {
            return redirect()
                ->route('register')
                ->withErrors(['email' => 'Choisissez un role puis continuez avec Google pour creer votre compte.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function createRoleProfile(User $user): void
    {
        if ($user->role === 'mentor') {
            Mentor::firstOrCreate(
                ['user_id' => $user->id],
                ['expertise_title' => 'A definir'],
            );
        }

        if ($user->role === 'mentee') {
            Mentee::firstOrCreate(['user_id' => $user->id]);
        }
    }
}
