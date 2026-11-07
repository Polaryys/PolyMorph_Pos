<x-app-layout>
    <!-- Encabezado y Acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('usuarios.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Agregar Usuario
        </a>
    </div>

    <!-- mensajes -->


    @if (session('success'))
        <div id="success-message" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
    @endif



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
            <th class="px-6 py-2.5 text-center w-64">Acciones</th>
        </x-slot:headers>

        @forelse ($usuarios as $usuario)
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

                <td class="w-64 whitespace-nowrap px-6 py-3">
                    <div class="flex items-center justify-center gap-3">
                        <x-action-icon element="a" href="{{ route('usuarios.show', $usuario) }}" wire:navigate
                            title="Ver usuario">
                            <x-lucide-eye class="h-5 w-5" />
                        </x-action-icon>

                        <x-action-icon element="a" href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
                            title="Editar usuario" color="green">
                            <x-lucide-pencil class="h-5 w-5" />
                        </x-action-icon>

                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                            onsubmit="return confirm('¿Deseas eliminar este usuario?')">
                            @csrf
                            @method('DELETE')

                            <x-action-icon type="submit" title="Eliminar usuario" color="red">
                                <x-lucide-trash-2 class="h-5 w-5" />
                            </x-action-icon>
                        </form>
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
