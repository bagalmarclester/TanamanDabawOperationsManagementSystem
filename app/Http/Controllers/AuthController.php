<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $loginValue = $request->input('login');
        $loginType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $loginValue,
            'password' => $request->input('password')
        ];


        $remember = $request->boolean('remember');


        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login successful'], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401)->onlyInput('login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
