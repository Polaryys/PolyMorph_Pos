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
    <x-table :paginator="$clientes">

        <x-slot:headers>
            <th class="px-6 py-2.5 w-24 text-center">No.</th>
            <th class="px-6 py-2.5 w-24 text-center">NIT</th>
            <th class="px-6 py-2.5">Nombres</th>
            <th class="px-6 py-2.5">Teléfono</th>
            <th class="px-6 py-2.5">Clasificación</th>
            <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Creado</th>
            <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Editado</th>
            <th class="px-6 py-2.5 text-center w-64">Acciones</th>
        </x-slot:headers>

        @forelse ($clientes as $cliente)
            <tr class="hover:bg-slate-50 transition-colors">

                <td class="px-6 py-3 w-24 text-center font-mono text-slate-500">
                    {{ ($clientes->currentPage() - 1) * $clientes->perPage() + $loop->iteration }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $cliente->nit }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $cliente->nombres }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $cliente->telefono }}
                </td>

                <td class="px-6 py-3 font-medium text-slate-900">
                    {{ $cliente->clasificacion }}
                </td>

                <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                    {{ $cliente->created_at->format('d/m/Y H:i') }}
                </td>

                <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                    {{ $cliente->updated_at->format('d/m/Y H:i') }}
                </td>

                <td class="px-6 py-3 w-64 text-center whitespace-nowrap space-x-8">

                    <a href="{{ route('clientes.show', $cliente) }}" wire:navigate
                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                        Ver
                    </a>

                    <a href="{{ route('clientes.edit', $cliente) }}" wire:navigate
                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                        Editar
                    </a>

                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('¿Deseas eliminar este cliente?')">

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
                <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                    No hay clientes registrados.
                </td>
            </tr>
        @endforelse

    </x-table>

</x-app-layout>
