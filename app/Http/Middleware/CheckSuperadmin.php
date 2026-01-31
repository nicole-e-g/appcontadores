<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('admin')->check() || auth()->guard('admin')->user()->rol !== 'superadmin') {
            // Si no es superadmin, lo redirigimos con un error
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        return $next($request);
    }
}
