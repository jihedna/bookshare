<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Entities\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use LaravelDoctrine\ORM\Facades\EntityManager;

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if email already exists
        $existingUser = EntityManager::getRepository(User::class)->findOneBy(['email' => $request->email]);
        if ($existingUser) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }

        // Create new user with Doctrine
        $user = new User();
        $user->setName($request->name);
        $user->setEmail($request->email);
        $user->setPassword(Hash::make($request->password));
        $user->setRoles(['ROLE_USER']);
        
        EntityManager::persist($user);
        EntityManager::flush();

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
