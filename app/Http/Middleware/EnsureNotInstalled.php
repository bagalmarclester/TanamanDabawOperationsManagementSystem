<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // If an Admin already exists, kick them to login
        if (User::where('is_admin', true)->exists()) {
            return redirect('/login');
        } 

        return $next($request);
    }
}