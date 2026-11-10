<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios.
     */
    public function index(): View
    {
        $usuarios = User::withCount([
            'ventas',
            'compras',
            'movimientos',
            'solicitudesIdempotencia',
        ])->orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(10);
        $administradoresActivos = User::query()
            ->where('rol', 'Administrador')
            ->where('estado', true)
            ->count();

        return view('usuarios.index', compact('usuarios', 'administradoresActivos'));
    }

    /**
     * Mostrar formulario para crear un usuario.
     */
    public function create(): View
    {
        return view('usuarios.create');
    }

    /**
     * Guardar un nuevo usuario.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
            ],

            'rol' => [
                'required',
                Rule::in(['Vendedor', 'Administrador']),
            ],

            'nombres' => [
                'required',
                'string',
                'max:100',
            ],

            'apellidos' => [
                'required',
                'string',
                'max:100',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'imagen_id' => [
                'nullable',
                'integer',
                'exists:imagenes,id',
            ],
        ]);

        User::create([
            'username' => $validated['username'],
            'rol' => $validated['rol'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'] ?? null,
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'],
            'estado' => true,
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Mostrar información de un usuario.
     */
    public function show(User $usuario): View
    {
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario para editar un usuario.
     */
    public function edit(User $usuario): View
    {
        $esUltimoAdministradorActivo = $usuario->rol === 'Administrador'
            && $usuario->estado
            && User::query()
                ->where('rol', 'Administrador')
                ->where('estado', true)
                ->count() === 1;

        return view('usuarios.edit', compact('usuario', 'esUltimoAdministradorActivo'));
    }

    /**
     * Actualizar un usuario.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($usuario->id),
            ],

            'rol' => [
                'required',
                Rule::in(['Vendedor', 'Administrador']),
            ],

            'nombres' => [
                'required',
                'string',
                'max:100',
            ],

            'apellidos' => [
                'required',
                'string',
                'max:100',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],

            'password' => [
                'nullable',
                'confirmed',
                'min:8',
            ],

            'estado' => [
                'required',
                'boolean',
            ],

            'imagen_id' => [
                'nullable',
                'integer',
                'exists:imagenes,id',
            ],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        DB::transaction(function () use ($usuario, $validated) {
            $administradoresActivos = User::query()
                ->where('rol', 'Administrador')
                ->where('estado', true)
                ->lockForUpdate()
                ->pluck('id');
            $usuarioActual = User::query()
                ->whereKey($usuario->id)
                ->lockForUpdate()
                ->firstOrFail();
            $seguiraSiendoAdministradorActivo = $validated['rol'] === 'Administrador'
                && (bool) $validated['estado'];

            if (
                $usuarioActual->rol === 'Administrador'
                && $usuarioActual->estado
                && ! $seguiraSiendoAdministradorActivo
                && ! $administradoresActivos->contains(
                    fn ($id) => (int) $id !== (int) $usuarioActual->id
                )
            ) {
                throw ValidationException::withMessages([
                    'estado' => 'No puedes desactivar ni cambiar el rol del único administrador activo.',
                ]);
            }

            $usuarioActual->update($validated);
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        $mensajeBloqueo = DB::transaction(function () use ($usuario) {
            $administradoresActivos = User::query()
                ->where('rol', 'Administrador')
                ->where('estado', true)
                ->lockForUpdate()
                ->pluck('id');
            $usuarioActual = User::query()
                ->whereKey($usuario->id)
                ->lockForUpdate()
                ->firstOrFail();

            $actividades = [];
            if ($usuarioActual->ventas()->exists()) {
                $actividades[] = 'ventas realizadas';
            }
            if ($usuarioActual->compras()->exists()) {
                $actividades[] = 'compras registradas';
            }
            if ($usuarioActual->movimientos()->exists()) {
                $actividades[] = 'movimientos de inventario';
            }
            if ($usuarioActual->solicitudesIdempotencia()->exists()) {
                $actividades[] = 'operaciones de venta registradas';
            }

            if ($actividades !== []) {
                return 'No se puede eliminar al usuario porque tiene '.implode(', ', $actividades)
                    .'. Desactívalo para conservar el historial.';
            }

            if (
                $usuarioActual->rol === 'Administrador'
                && $usuarioActual->estado
                && ! $administradoresActivos->contains(
                    fn ($id) => (int) $id !== (int) $usuarioActual->id
                )
            ) {
                return 'No se puede eliminar al único administrador activo. Activa o crea otro administrador primero.';
            }

            $usuarioActual->delete();

            return null;
        });

        if ($mensajeBloqueo !== null) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', $mensajeBloqueo);
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
