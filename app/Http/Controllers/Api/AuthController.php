<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\Core\AccesoService;
use App\Services\Core\AuditoriaService;
use App\Services\Core\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_INTENTOS = 5;

    private const MINUTOS_BLOQUEO = 5;

    public function __construct(
        private AuditoriaService $auditoria,
        private MenuService $menuService,
        private AccesoService $accesoService,
    ) {}

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

        $this->auditoria->registrar('NUCLEO', 'login_exitoso', 'usuario', $usuario->id, null, $usuario, $request);

        return response()->json($this->userPayload($usuario));
    }

    public function logout(Request $request)
    {
        $usuario = $request->user();
        if ($usuario instanceof Usuario) {
            $this->auditoria->registrar('NUCLEO', 'logout', 'usuario', $usuario->id, null, $usuario, $request);
        }

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
        $usuario->load(['unidadActiva', 'roles']);

        return [
            'id' => $usuario->id,
            'username' => $usuario->username,
            'nombre_completo' => $usuario->nombre_completo,
            'email' => $usuario->email,
            'unidad_activa_id' => $usuario->unidad_activa_id,
            'unidad' => $usuario->unidadActiva ? [
                'id' => $usuario->unidadActiva->id,
                'codigo_org' => $usuario->unidadActiva->codigo_org,
                'nombre' => $usuario->unidadActiva->nombre,
            ] : null,
            'roles' => $usuario->roles->map(fn ($r) => [
                'id' => $r->id,
                'codigo' => $r->codigo,
                'nombre' => $r->nombre,
            ]),
            'permisos' => $usuario->permisosCodigos(),
            'puede_operar_documentaria' => $this->accesoService->puedeOperarDocumentaria($usuario),
            'vista_ejecutiva' => $this->accesoService->esVistaEjecutiva($usuario),
            'menu' => $this->menuService->forUsuario($usuario),
            'nucleo_menu' => $this->menuService->nucleoSubmenu($usuario),
            'patrimonio_menu' => $this->menuService->patrimonioSubmenu($usuario),
            'calidad_menu' => $this->menuService->calidadSubmenu($usuario),
        ];
    }
}
