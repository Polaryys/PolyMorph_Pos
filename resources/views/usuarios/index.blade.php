<x-app-layout>
    <!-- Encabezado y Acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('usuarios.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Agregar Usuario
        </a>
    </div>

    <!-- MENSAJES -->
    <x-alert-message />

    <!-- tabla de usuarios -->
    <x-table :paginator="$usuarios">

        <x-slot:headers>
            <th class="px-6 py-2.5 w-24 text-center">No.</th>
            <th class="px-6 py-2.5">Usuario</th>
            <th class="px-6 py-2.5">Nombres</th>
            <th class="px-6 py-2.5">Apellidos</th>
            <th class="px-6 py-2.5 text-center">Rol</th>
            <th class="px-6 py-2.5 text-center">Estado</th>
            <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Creado</th>
            <th class="px-6 py-2.5 text-center w-32">Acciones</th>
        </x-slot:headers>

        @forelse ($usuarios as $usuario)
            @php
                $razonesBloqueoEliminacion = [];

                if ($usuario->ventas_count > 0) {
                    $razonesBloqueoEliminacion[] = 'tiene ventas registradas';
                }
                if ($usuario->compras_count > 0) {
                    $razonesBloqueoEliminacion[] = 'tiene compras registradas';
                }
                if ($usuario->movimientos_count > 0) {
                    $razonesBloqueoEliminacion[] = 'tiene movimientos de inventario';
                }
                if ($usuario->solicitudes_idempotencia_count > 0) {
                    $razonesBloqueoEliminacion[] = 'tiene operaciones de venta registradas';
                }
                if (
                    $usuario->rol === 'Administrador'
                    && $usuario->estado
                    && $administradoresActivos === 1
                ) {
                    $razonesBloqueoEliminacion[] = 'es el único administrador activo';
                }
                $mensajeBloqueoEliminacion = 'No se puede eliminar: '
                    . implode('; ', $razonesBloqueoEliminacion) . '.';
            @endphp
            <tr class="hover:bg-slate-50 transition-colors">

                <td class="px-6 py-3 w-24 text-center font-mono text-slate-500">
                    {{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $usuario->username }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $usuario->nombres }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $usuario->apellidos }}
                </td>

                <td class="px-6 py-3 text-center">
                    {{ $usuario->rol }}
                </td>

                <td class="px-6 py-3 text-center">
                    @if ($usuario->estado)
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Activo
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                            Inactivo
                        </span>
                    @endif
                </td>

                <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                    {{ $usuario->created_at->format('d/m/Y H:i') }}
                </td>

                <td class="w-32 whitespace-nowrap px-6 py-3">
                    <div class="flex items-center justify-center gap-3">
                        <x-action-icon element="a" href="{{ route('usuarios.show', $usuario) }}" wire:navigate
                            title="Ver usuario">
                            <x-lucide-eye class="h-5 w-5" />
                        </x-action-icon>

                        <x-action-icon element="a" href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
                            title="Editar usuario" color="green">
                            <x-lucide-pencil class="h-5 w-5" />
                        </x-action-icon>

                        @if ($razonesBloqueoEliminacion === [])
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')

                                <x-action-icon type="submit" title="Eliminar usuario" color="red">
                                    <x-lucide-trash-2 class="h-5 w-5" />
                                </x-action-icon>
                            </form>
                        @else
                            <span title="{{ $mensajeBloqueoEliminacion }}" class="inline-flex cursor-not-allowed">
                                <x-action-icon type="button" :title="$mensajeBloqueoEliminacion" color="red"
                                    disabled class="cursor-not-allowed opacity-40">
                                    <x-lucide-trash-2 class="h-5 w-5" />
                                </x-action-icon>
                            </span>
                        @endif
                    </div>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                    No hay usuarios registrados.
                </td>
            </tr>
        @endforelse

    </x-table>

</x-app-layout>
