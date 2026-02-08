<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SetupController extends Controller
{
    public function index()
    {
        // Check if there is already role admin exist in the database
        $isAdminExists = User::where('role', 'Admin')->exists();
        if ($isAdminExists) {
            return redirect('/');
        }
        return view('setup.install');
    }
    public function store(Request $request)
    {
        try {
            // Check if there is already role admin exist in the database
            $isAdminExists = User::where('role', 'Admin')->exists();
            if ($isAdminExists) {
                return response()->json([
                    'message' => 'Forbidden action. This can only be done once',
                    'redirect' => route('/')
                ], 403);
            }

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Admin',
            ]);

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'message' => 'Admin created successfully',
                'redirect' => route('dashboard')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
