<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRememberMeExpiration
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('web');

        $user = $guard->user();

        // Remember Me로 자동 로그인된 경우에만 만료일 확인
        if (
            $user &&
            $guard->viaRemember() &&
            $user->remember_expires_at &&
            $user->remember_expires_at->isPast()
        ) {
            $guard->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('status', 'Your session has expired. Please log in again.');
        }

        return $next($request);
    }
}