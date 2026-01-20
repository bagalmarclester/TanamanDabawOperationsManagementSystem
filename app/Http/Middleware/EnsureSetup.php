<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $isAdminExists = User::where('is_admin', true)->exists();


        if (!$isAdminExists) {


            if ($request->is('setup') || $request->is('setup/*') || $request->is('register')) {
                return $next($request);
            }


            return redirect('/setup');
        }

        return $next($request);
    }
}
