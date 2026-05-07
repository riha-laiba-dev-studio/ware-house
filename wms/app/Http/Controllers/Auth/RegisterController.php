<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => true,
        ]);

        // Default role for self-registered users (keep permissions consistent).
        $defaultRole = Role::where('guard_name', 'web')->whereIn('name', ['Staff', 'Manager'])->first();
        if ($defaultRole) {
            $user->syncRoles([$defaultRole->name]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Welcome! Your account has been created.');
    }
}

