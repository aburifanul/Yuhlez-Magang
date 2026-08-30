<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Pastikan user sudah login
        |--------------------------------------------------------------------------
        */

        if (!$request->user()) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil role user
        |--------------------------------------------------------------------------
        */

        $userRole = $request->user()->role;

        /*
        |--------------------------------------------------------------------------
        | Konversi Enum menjadi string
        |--------------------------------------------------------------------------
        |
        | Karena User model kita menggunakan:
        |
        | 'role' => UserRole::class
        |
        | maka $userRole adalah UserRole enum.
        |
        */

        $userRoleValue = $userRole instanceof UserRole
            ? $userRole->value
            : $userRole;

        /*
        |--------------------------------------------------------------------------
        | Cek apakah role user diperbolehkan
        |--------------------------------------------------------------------------
        */

        if (!in_array($userRoleValue, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}