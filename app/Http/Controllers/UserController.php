<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios.
     */
    public function index(): View
    {
        $usuarios = User::orderBy('nombres')
            ->orderBy('apellidos')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
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
        return view('usuarios.edit', compact('usuario'));
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

        $usuario->update($validated);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}