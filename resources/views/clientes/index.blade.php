<x-app-layout>
    <!-- Encabezado y Acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('clientes.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Agregar Cliente
        </a>
    </div>

    <!-- mensajes -->
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- tabla de clientes -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-2.5 w-24 text-center">No.</th>
                        <th class="px-6 py-2.5 w-24 text-center">NIT</th>
                        <th class="px-6 py-2.5">Nombres</th>
                        <th class="px-6 py-2.5">Telefono</th>
                        <th class="px-6 py-2.5">Clasificación</th>
                        <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">CREADO</th>
                        <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">EDITADO</th>
                        <th class="px-6 py-2.5 text-center w-64">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    @forelse ($clientes as $cliente)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 w-24 text-center font-mono text-gray-500">
                                {{ ($clientes->currentPage() - 1) * $clientes->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $cliente->nit }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $cliente->nombres }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $cliente->telefono }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $cliente->clasificacion }}</td>
                            <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-gray-400 italic">
                                {{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-gray-400 italic">
                                {{ $cliente->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 w-64 text-center whitespace-nowrap space-x-8">
                                <a href="{{ route('clientes.show', $cliente) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Ver
                                </a>
                                
                                <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Editar
                                </a>
                                

                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('¿Deseas eliminar este cliente?')">
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
                                No hay clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- paginacion de resultados -->
            @if ($clientes->hasPages())
                <div class="px-5 py-2 border-t border-gray-200">
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>

</x-app-layout>
