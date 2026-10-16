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
    <div class="bg-white border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-2.5 w-24 text-center">No.</th>
                        <th class="px-6 py-2.5">Usuario</th>
                        <th class="px-6 py-2.5">Nombres</th>
                        <th class="px-6 py-2.5">Apellidos</th>
                        <th class="px-6 py-2.5 text-center">Rol</th>
                        <th class="px-6 py-2.5 text-center">Estado</th>
                        <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">CREADO</th>
                        <th class="px-6 py-2.5 text-center w-64">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse ($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 transition-colors">

                            <td class="px-6 py-3 w-24 text-center font-mono text-gray-500">
                                {{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $loop->iteration }}
                            </td>

                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $usuario->username }}
                            </td>

                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $usuario->nombres }}
                            </td>

                            <td class="px-6 py-3 font-medium text-gray-900">
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

                            <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-gray-400 italic">
                                {{ $usuario->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-6 py-3 w-64 text-center whitespace-nowrap space-x-8">

                                <a href="{{ route('usuarios.show', $usuario) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Ver
                                </a>

                                <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Editar
                                </a>

                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('¿Deseas eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                        Eliminar
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- paginacion de resultados -->
            @if ($usuarios->hasPages())
                <div class="px-5 py-2 border-t border-gray-200">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
