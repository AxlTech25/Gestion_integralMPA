<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario || ! $usuario->hasPermiso($permission)) {
            return response()->json([
                'message' => 'No tiene permiso para realizar esta acción.',
            ], 403);
        }

        return $next($request);
    }
}
