<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Pakai:
 *   ->middleware('role:Super Admin')
 *   ->middleware('role:Admin Gudang,Kepala Lab')
 *   ->middleware('role:Admin Gudang|Kepala Lab')
 *   ->middleware('role:Super Admin|Admin Gudang|Kepala Lab|Petugas Unit')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $user      = Auth::user();
        $userRole  = strtolower(optional($user->role)->role_name ?? '');
        $allowed   = collect($roles)
            ->flatMap(function ($r) {
                // dukung pemisah koma dan pipa
                return preg_split('/[|,]/', (string) $r);
            })
            ->map(fn($r) => trim(strtolower($r)))
            ->filter()
            ->values();

        // Super Admin selalu boleh lewat
        if ($userRole === 'super admin') {
            return $next($request);
        }

        if ($allowed->isNotEmpty() && !$allowed->contains($userRole)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
