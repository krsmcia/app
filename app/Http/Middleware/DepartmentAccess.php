<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentAccess
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$departments
    ): Response {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        // Super Admin은 모든 부서 접근 가능
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }
        // 사용자가 해당 부서 중 하나에 속해 있는지 확인
        if ($user->departments()->whereIn('code', $departments)->exists()) {
            return $next($request);
        }
        abort(403);
    }
}