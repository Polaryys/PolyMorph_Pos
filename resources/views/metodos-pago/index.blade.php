<x-app-layout>
    <div x-data="{
        abierto: {{ $errors->any() ? 'true' : 'false' }},
        modo: @js(old('modal_mode', 'crear')),
        nombre: @js(old('nombre', '')),
        descripcion: @js(old('descripcion', '')),
        tipoCaptura: @js(old('tipo_captura', 'PERMITIR REFERENCIA')),
        estado: @js(old('estado', '1')),
        action: @js(old('form_action', route('metodos-pago.store'))),
        editar: @js(old('form_method', 'POST') === 'PUT')
    }" @keydown.escape.window="abierto = false">

        <div class="mb-3 flex items-center justify-between">
            <button type="button"
                @click="
                    modo = 'crear';
                    nombre = '';
                    descripcion = '';
                    tipoCaptura = 'PERMITIR REFERENCIA';
                    estado = '1';
                    action = {{ Js::from(route('metodos-pago.store')) }};
                    editar = false;
                    abierto = true
                "
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
                + Nuevo Método de Pago
            </button>
        </div>

        <!-- MENSAJES -->
        <x-alert-message />

        <x-table :paginator="$metodosPago">
            <x-slot:headers>
                <th class="w-24 px-6 py-2.5 text-center">No.</th>
                <th class="px-6 py-2.5">Nombre</th>
                <th class="px-6 py-2.5">Descripción</th>
                <th class="px-6 py-2.5 text-center">Captura</th>
                <th class="px-6 py-2.5 text-center">Estado</th>
                <th class="w-40 whitespace-nowrap px-6 py-2.5 text-center">Creado</th>
                <th class="w-40 whitespace-nowrap px-6 py-2.5 text-center">Actualizado</th>
                <th class="w-40 px-6 py-2.5 text-center">Acciones</th>
            </x-slot:headers>

            @forelse ($metodosPago as $metodoPago)
                <tr class="transition-colors hover:bg-slate-50">
                    <td class="w-24 px-6 py-3 text-center font-mono text-slate-500">
                        {{ ($metodosPago->currentPage() - 1) * $metodosPago->perPage() + $loop->iteration }}
                    </td>

                    <td class="px-6 py-3 font-medium text-slate-900">
                        {{ $metodoPago->nombre }}
                    </td>

                    <td class="max-w-sm px-6 py-3 text-slate-500">
                        <span class="block truncate" title="{{ $metodoPago->descripcion }}">
                            {{ $metodoPago->descripcion ?: 'Sin descripción' }}
                        </span>
                    </td>

                    <td class="px-6 py-3 text-center">
                        {{ $metodoPago->tipo_captura }}
                    </td>

                    <td class="px-6 py-3 text-center">
                        <span @class([
                            'inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                            'bg-green-100 text-green-700' => $metodoPago->estado,
                            'bg-slate-100 text-slate-600' => !$metodoPago->estado,
                        ])>
                            {{ $metodoPago->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>

                    <td class="w-40 whitespace-nowrap px-6 py-3 text-center italic text-slate-400">
                        {{ $metodoPago->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="w-40 whitespace-nowrap px-6 py-3 text-center italic text-slate-400">
                        {{ $metodoPago->updated_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="w-40 whitespace-nowrap px-6 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <x-action-icon
                                @click="
                                modo = 'editar';
                                nombre = {{ Js::from($metodoPago->nombre) }};
                                descripcion = {{ Js::from($metodoPago->descripcion ?? '') }};
                                tipoCaptura = {{ Js::from($metodoPago->tipo_captura) }};
                                estado = {{ Js::from((string) (int) $metodoPago->estado) }};
                                action = {{ Js::from(route('metodos-pago.update', $metodoPago)) }};
                                editar = true;
                                abierto = true
                            "
                                title="Editar método de pago" color="green">
                                <x-lucide-pencil class="h-5 w-5" />
                            </x-action-icon>

                            <form action="{{ route('metodos-pago.destroy', $metodoPago) }}" method="POST"
                                onsubmit="return confirm('¿Deseas eliminar este método de pago?')">
                                @csrf
                                @method('DELETE')

                                <x-action-icon type="submit" title="Eliminar método de pago" color="red">
                                    <x-lucide-trash-2 class="h-5 w-5" />
                                </x-action-icon>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                        No se encontraron métodos de pago registrados.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <template x-teleport="body">
            <div x-cloak x-show="abierto" x-transition.opacity
                class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4"
                @click.self="abierto = false">
                <div x-show="abierto" x-transition
                    class="w-full max-w-md rounded-lg border border-gray-200 bg-white p-6 shadow-xl" role="dialog"
                    aria-modal="true" aria-labelledby="titulo-modal-metodo-pago" @click.stop>

                    <h2 id="titulo-modal-metodo-pago"
                        class="mb-5 border-b border-gray-200 pb-3 text-lg font-semibold text-indigo-600"
                        x-text="modo === 'editar' ? 'Editar Método de Pago' : 'Registrar Nuevo Método de Pago'">
                    </h2>

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
                                Nombre del método de pago
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" x-model="nombre" maxlength="50" required
                                x-effect="if (abierto) $nextTick(() => $el.focus())"
                                class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nombre')
border-red-500
@enderror">
                            @error('nombre')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="descripcion" class="mb-1 block text-sm font-medium text-gray-700">
                                Descripción
                            </label>
                            <textarea id="descripcion" name="descripcion" x-model="descripcion" rows="3" maxlength="255"
                                class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('descripcion') border-red-500 @enderror"></textarea>
                            @error('descripcion')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tipo_captura" class="mb-1 block text-sm font-medium text-gray-700">
                                Captura del pago
                                <span class="text-red-500">*</span>
                            </label>
                            <select id="tipo_captura" name="tipo_captura" x-model="tipoCaptura" required
                                class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tipo_captura') border-red-500 @enderror">
                                <option value="PERMITIR MONTOS">PERMITIR MONTOS (recibido y cambio)</option>
                                <option value="PERMITIR REFERENCIA">PERMITIR REFERENCIA</option>
                            </select>
                            @error('tipo_captura')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="mb-1 block text-sm font-medium text-gray-700">
                                Estado
                                <span class="text-red-500">*</span>
                            </label>
                            <select id="estado" name="estado" x-model="estado" required
                                class="w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('estado') border-red-500 @enderror">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                            <button type="button" @click="abierto = false"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                                x-text="modo === 'editar' ? 'Guardar cambios' : 'Guardar Método de Pago'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
