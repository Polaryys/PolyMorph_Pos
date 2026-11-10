<x-app-layout>
    <!-- Encabezado y Acción -->
    <x-primary-link href="{{ route('clientes.create') }}" wire:navigate class="mb-3">
        + Agregar Cliente
    </x-primary-link>

    <!-- MENSAJES -->
    <x-alert-message />

    <!-- tabla de clientes -->
    <x-table :paginator="$clientes">

        <x-slot:headers>
            <th class="px-6 py-2.5 w-16 text-center">No.</th>
            <th class="px-6 py-2.5 text-left">NIT</th>
            <th class="px-6 py-2.5">Nombres</th>
            <th class="px-6 py-2.5">Teléfono</th>
            <th class="px-6 py-2.5 w-16">Clasificación</th>
            <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Creado</th>
            <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Editado</th>
            <th class="px-6 py-2.5 text-center w-40">Acciones</th>
        </x-slot:headers>

        @forelse ($clientes as $cliente)
            <tr class="hover:bg-slate-50 transition-colors">

                <td class="px-6 py-3 w-16 text-center font-mono text-slate-500">
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

                <td class="px-6 py-3 w-16 font-medium text-slate-900">
                    {{ $cliente->clasificacion }}
                </td>

                <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                    {{ $cliente->created_at->format('d/m/Y H:i') }}
                </td>

                <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                    {{ $cliente->updated_at->format('d/m/Y H:i') }}
                </td>

                <td class="w-40 whitespace-nowrap px-6 py-3">
                    <div class="flex items-center justify-center gap-3">
                        <x-action-icon element="a" href="{{ route('clientes.show', $cliente) }}" wire:navigate
                            title="Ver cliente">
                            <x-lucide-eye class="h-5 w-5" />
                        </x-action-icon>

                        <x-action-icon element="a" href="{{ route('clientes.edit', $cliente) }}" wire:navigate
                            title="Editar cliente" color="green">
                            <x-lucide-pencil class="h-5 w-5" />
                        </x-action-icon>

                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                            onsubmit="return confirm('¿Deseas eliminar este cliente?')">
                            @csrf
                            @method('DELETE')

                            <x-action-icon type="submit" title="Eliminar cliente" color="red">
                                <x-lucide-trash-2 class="h-5 w-5" />
                            </x-action-icon>
                        </form>
                    </div>
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
