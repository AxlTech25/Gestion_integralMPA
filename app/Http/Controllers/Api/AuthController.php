<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_INTENTOS = 5;

    private const MINUTOS_BLOQUEO = 5;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('username', $credentials['username'])->first();

        if (! $usuario || ! $usuario->activo) {
            throw ValidationException::withMessages([
                'username' => ['Credenciales inválidas.'],
            ]);
        }

        if ($usuario->bloqueado_hasta && $usuario->bloqueado_hasta->isFuture()) {
            throw ValidationException::withMessages([
                'username' => ['Cuenta bloqueada temporalmente. Intente más tarde.'],
            ]);
        }

        if (! Hash::check($credentials['password'], $usuario->password)) {
            $usuario->intentos_fallidos++;

            if ($usuario->intentos_fallidos >= self::MAX_INTENTOS) {
                $usuario->bloqueado_hasta = now()->addMinutes(self::MINUTOS_BLOQUEO);
                $usuario->intentos_fallidos = 0;
            }

            $usuario->save();

            throw ValidationException::withMessages([
                'username' => ['Credenciales inválidas.'],
            ]);
        }

        $usuario->forceFill([
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null,
            'ultimo_login' => now(),
        ])->save();

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        return response()->json($this->userPayload($usuario));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function user(Request $request)
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario) {
            return response()->json(null, 401);
        }

        return response()->json($this->userPayload($usuario));
    }

    private function userPayload(Usuario $usuario): array
    {
        return [
            'id' => $usuario->id,
            'username' => $usuario->username,
            'nombre_completo' => $usuario->nombre_completo,
            'email' => $usuario->email,
            'unidad_activa_id' => $usuario->unidad_activa_id,
        ];
    }
}
