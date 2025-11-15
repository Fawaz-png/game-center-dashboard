<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Only allow assigning a role when the current user is an admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            $rules['role_ids'] = 'nullable|array';
            $rules['role_ids.*'] = 'string|exists:roles,id';
        } else {
            // Prevent role assignment by non-admins
            $rules['role_ids'] = 'prohibited';
        }

        $request->validate($rules);

        // In production, only admins are allowed to create other users
        if (app()->environment('production') && (! Auth::check() || ! Auth::user()->isAdmin())) {
            abort(403, 'User registration is disabled in production. Contact an administrator.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // If an admin provided roles, attach them via the pivot table
        if ($request->filled('role_ids') && Auth::check() && Auth::user()->isAdmin()) {
            $user->roles()->sync($request->input('role_ids'));
        }

        event(new Registered($user));

        // Only log the user in automatically if they self-registered (not created by an admin)
        if (! (Auth::check() && Auth::user()->isAdmin())) {
            Auth::login($user);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
