<x-app-layout>
    <div x-data="{
        abierto: {{ $errors->any() ? 'true' : 'false' }},
        modo: @js(old('modal_mode', 'crear')),
        nombre: @js(old('nombre', '')),
        action: @js(old('form_action', route('marcas.store'))),
        editar: @js(old('form_method', 'POST') === 'PUT')
    }" @keydown.escape.window="abierto = false">

        <div class="flex items-center justify-between mb-3">
            <button type="button"
                @click="
                    modo = 'crear';
                    nombre = '';
                    action = {{ Js::from(route('marcas.store')) }};
                    editar = false;
                    abierto = true
                "
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium rounded-md shadow-sm transition">
                + Nueva Marca
            </button>
        </div>

        <!-- MENSAJES -->
        <x-alert-message />

        <!-- TABLA -->
        <x-table :paginator="$marcas">

            <x-slot:headers>
                <th class="px-6 py-2.5 w-24 text-center">No.</th>
                <th class="px-6 py-2.5">Nombre</th>
                <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Creado</th>
                <th class="px-6 py-2.5 text-center whitespace-nowrap w-40">Actualizado</th>
                <th class="px-6 py-2.5 text-center w-64">Acciones</th>
            </x-slot:headers>

            @forelse ($marcas as $marca)
                <tr class="hover:bg-slate-50 transition-colors">

                    <td class="px-6 py-3 w-24 text-center font-mono text-slate-500">
                        {{ ($marcas->currentPage() - 1) * $marcas->perPage() + $loop->iteration }}
                    </td>

                    <td class="px-6 py-3 font-medium text-slate-900">
                        {{ $marca->nombre }}
                    </td>

                    <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                        {{ $marca->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="px-6 py-3 w-40 text-center whitespace-nowrap text-slate-400 italic">
                        {{ $marca->updated_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="w-64 whitespace-nowrap px-6 py-3">
                        <div class="flex items-center justify-center gap-3">
                            <x-action-icon
                                @click="
                                    modo = 'editar';
                                    nombre = {{ Js::from($marca->nombre) }};
                                    action = {{ Js::from(route('marcas.update', $marca)) }};
                                    editar = true;
                                    abierto = true
                                "
                                title="Editar marca" color="green">
                                <x-lucide-pencil class="h-5 w-5" />
                            </x-action-icon>

                            <form action="{{ route('marcas.destroy', $marca) }}" method="POST"
                                onsubmit="return confirm('¿Deseas eliminar esta marca?')">
                                @csrf
                                @method('DELETE')

                                <x-action-icon type="submit" title="Eliminar marca" color="red">
                                    <x-lucide-trash-2 class="h-5 w-5" />
                                </x-action-icon>
                            </form>
                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                        No se encontraron marcas registradas.
                    </td>
                </tr>
            @endforelse

        </x-table>

        <!-- MODAL CREAR / EDITAR -->
        <template x-teleport="body">
            <div x-cloak x-show="abierto" x-cloak x-transition.opacity
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
                @click.self="abierto = false">
                <div x-show="abierto" x-transition
                    class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-xl" role="dialog"
                    aria-modal="true" aria-labelledby="titulo-modal-marca">

                    <!-- TÍTULO -->
                    <h2 id="titulo-modal-marca"
                        class="mb-5 border-b border-gray-200 pb-3 text-lg font-semibold text-indigo-600"
                        x-text="modo === 'editar' ? 'Editar Marca' : 'Registrar Nueva Marca'">
                    </h2>

                    <!-- FORMULARIO -->
                    <form :action="action" method="POST" class="space-y-4">
                        @csrf

                        <input type="hidden" name="modal_mode" :value="modo">
                        <input type="hidden" name="form_action" :value="action">
                        <input type="hidden" name="form_method" :value="editar ? 'PUT' : 'POST'">

                        <template x-if="editar">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div>
                            <label for="nombre" class="mb-1 block text-sm font-medium text-gray-700">
                                Nombre de la marca
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" id="nombre" name="nombre" x-model="nombre" maxlength="55" required
                                x-effect="if (abierto) $nextTick(() => $el.focus())"
                                class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   @error('nombre')
border-red-500
@enderror">

                            @error('nombre')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- BOTONES -->
                        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">

                            <button type="button" @click="abierto = false"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium
                                   text-gray-700 transition hover:bg-gray-50">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium
                                   text-white shadow-sm transition hover:bg-indigo-700"
                                x-text="modo === 'editar' ? 'Guardar cambios' : 'Guardar Marca'">
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
