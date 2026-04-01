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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:mentor,mentee'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($user->role === 'mentor') {
            Mentor::create([
                'user_id' => $user->id,
                'expertise_title' => 'A definir',
            ]);
        }

        if ($user->role === 'mentee') {
            Mentee::create([
                'user_id' => $user->id,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'mentor') {
            return redirect()->route('mentor.dashboard');
        }

        if ($user->role === 'mentee') {
            return redirect()->route('mentee.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
