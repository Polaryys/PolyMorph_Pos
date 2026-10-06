<x-app-layout>
    <!-- Encabezado y Acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('marcas.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Nueva Marca
        </a>
    </div>

    <!-- mensajes -->
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- tabla de marcas -->
    <div class="bg-white border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-2.5 w-24 text-center">No.</th>
                        <th class="px-6 py-2.5">Nombre</th>
                        <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">CREADO</th>
                        <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">ACTUALIZADO</th>
                        <th class="px-6 py-2.5 text-center w-64">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse ($marcas as $marca)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 w-24 text-center font-mono text-gray-500">
                                {{ ($marcas->currentPage() - 1) * $marcas->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $marca->nombre }}</td>
                            <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-gray-400 italic">
                                {{ $marca->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-gray-400 italic">
                                {{ $marca->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 w-64 text-center whitespace-nowrap space-x-8">
                                <a href="{{ route('marcas.edit', $marca) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Editar
                                </a>

                                <form action="{{ route('marcas.destroy', $marca) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('¿Deseas eliminar esta marca?')">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                No se encontraron marcas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- paginacion de resultados -->
            @if ($marcas->hasPages())
                <div class="px-5 py-2 border-t border-gray-200">
                    {{ $marcas->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
