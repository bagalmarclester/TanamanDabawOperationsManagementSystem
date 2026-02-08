<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:500',
            ]);

            $user->fill($validated)->save();

            return back()->with('status', 'profile-updated');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error has occurred. Please contact the developer. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = $request->user();
        
        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return back()->with('status', 'password-updated');
    }
}
