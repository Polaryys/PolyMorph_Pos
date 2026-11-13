<x-app-layout>
    <div x-data="{
        modalImportacionAbierto: {{ old('modal_importacion') ? 'true' : 'false' }},
        importandoInventario: false,
        archivoSeleccionado: '',

        iniciarImportacion(event) {
            if (this.importandoInventario || !this.archivoSeleccionado) {
                event.preventDefault();
                return;
            }

            this.importandoInventario = true;
        },

        cerrarModalImportacion() {
            if (this.importandoInventario) return;

            this.modalImportacionAbierto = false;
            this.$refs.archivoInventario.value = '';
            this.archivoSeleccionado = '';
        },
    }" @keydown.escape.window="if (modalImportacionAbierto) cerrarModalImportacion()">

    <!-- Encabezado y acciones -->
    <div class="mb-5">

        <x-search-bar :action="route('articulos.index')" :buscar="$buscar" placeholder="Buscar artículos por nombre, código o marca">

            <x-primary-link href="{{ route('articulos.create') }}" wire:navigate>
                + Agregar Artículos
            </x-primary-link>

        </x-search-bar>

    </div>

    <!-- MENSAJES -->
    <x-alert-message />

    <!-- TABLA DE ARTÍCULOS -->
    <x-table :paginator="$articulos">

        <x-slot:headers>
            <th class="text-center w-16 whitespace-nowrap">Imagen</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">SKU</th>
            <th class="px-2 py-3 text-left whitespace-nowrap">BARCODE</th>
            <th class="px-4 py-3 text-left whitespace-nowrap">Nombre</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Marca</th>
            <th class="px-4 py-3 text-center whitespace-nowrap">Precio U.</th>
            <th class="px-4 py-3 w-16 text-center whitespace-nowrap">Cantidad</th>
            <th class="px-4 py-3 w-16 text-center whitespace-nowrap">Acciones</th>
        </x-slot:headers>

        @forelse ($articulos as $articulo)
            <tr class="hover:bg-indigo-50/50 transition-colors">
                <!-- IMAGEN -->
                <td class="px-2 py-2 text-center">
                    @if ($articulo->presentaciones->first()?->imagen)
                        <img src="{{ asset('storage/imagenes/' . $articulo->presentaciones->first()->imagen->nombre_archivo) }}"
                            alt="{{ $articulo->presentaciones->first()->nombre }}"
                            class="w-16 h-16 object-cover rounded border border-slate-200 mx-auto">
                    @else
                        <div
                            class="w-16 h-16 mx-auto flex items-center justify-center bg-slate-100 rounded text-slate-400">
                            <x-lucide-image class="w-7 h-7" />
                        </div>
                    @endif
                </td>

                <!-- SKU -->
                <td class="px-4 py-3 font-medium text-slate-900 whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->codigoSKU }}
                </td>

                <!-- CÓDIGO DE BARRAS -->
                <td class="text-slate-500 text-sm whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->codigoBarras }}
                </td>

                <!-- NOMBRE -->
                <td class="px-4 py-3 text-slate-900 whitespace-nowrap">
                    {{ $articulo->presentaciones->first()?->nombre }}
                </td>

                <!-- MARCA -->
                <td class="px-4 py-3 text-center font-medium text-slate-700 whitespace-nowrap">
                    {{ $articulo->marca?->nombre }}
                </td>

                <!-- PRECIO DE VENTA -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    Q {{ number_format((float) ($articulo->presentaciones->first()?->precioUnitario ?? 0), 2) }}
                </td>

                <!-- CANTIDAD -->
                <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                    {{ $articulo->cantidad }}
                </td>

                <!-- ACCIONES -->
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-3">

                        <!-- Ver -->
                        <x-action-icon element="a" href="{{ route('articulos.show', $articulo) }}" wire:navigate
                            title="Ver artículo">
                            <x-lucide-eye class="w-5 h-5" />
                        </x-action-icon>

                        <!-- Editar -->
                        <x-action-icon element="a" href="{{ route('articulos.edit', $articulo) }}" wire:navigate
                            title="Editar artículo" color="green">
                            <x-lucide-pencil class="w-5 h-5" />
                        </x-action-icon>

                        <!-- Eliminar -->
                        <form action="{{ route('articulos.destroy', $articulo) }}" method="POST"
                            onsubmit="return confirm('¿Deseas eliminar este artículo? Esta acción eliminará toda presentación asociada al artículo seleccionado')">

                            @csrf
                            @method('DELETE')

                            <x-action-icon type="submit" title="Eliminar artículo" color="red">
                                <x-lucide-trash-2 class="w-5 h-5" />
                            </x-action-icon>
                        </form>

                    </div>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                    No se encontraron artículos.
                </td>
            </tr>
        @endforelse

    </x-table>

    <div class="mt-4 flex flex-col justify-end gap-2 sm:flex-row">
        <x-secondary-link href="{{ route('articulos.exportar-csv') }}">
            Exportar CSV
        </x-secondary-link>
        <button type="button" @click="modalImportacionAbierto = true"
            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            Importar inventario inicial
        </button>
    </div>

    <template x-teleport="body">
        <div x-cloak x-show="modalImportacionAbierto" x-transition.opacity
            class="fixed inset-0 z-100 flex items-center justify-center bg-slate-900/60 p-4"
            @click.self="cerrarModalImportacion()">
            <div x-show="modalImportacionAbierto" x-transition
                class="w-full max-w-lg overflow-hidden rounded-xl bg-white p-6 shadow-2xl"
                role="dialog" aria-modal="true" aria-labelledby="titulo-modal-importacion" @click.stop>
                <h2 id="titulo-modal-importacion"
                    class="mb-4 border-b border-slate-200 pb-3 text-lg font-semibold text-slate-900">
                    Importar inventario inicial
                </h2>

                <form action="{{ route('articulos.importar-csv') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4" @submit="iniciarImportacion($event)">
                    @csrf
                    <input type="hidden" name="modal_importacion" value="1">

                    @if ($errors->has('archivo'))
                        <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                            <p class="font-medium">No se pudo importar el inventario:</p>
                            <ul class="mt-1 list-inside list-disc">
                                @foreach ($errors->get('archivo') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="archivo-inventario" class="mb-1 block text-sm font-medium text-slate-700">
                            Archivo CSV
                        </label>
                        <input x-ref="archivoInventario" type="file" id="archivo-inventario" name="archivo"
                            accept=".csv,text/csv" required
                            @change="archivoSeleccionado = $event.target.files.length ? $event.target.files[0].name : ''"
                            class="block w-full rounded-md border border-slate-300 text-sm text-slate-600 file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-2 text-xs text-slate-500">
                            Sube el CSV de inventario con las columnas esperadas. Tamaño máximo: 10 MB.
                        </p>
                        <p x-show="archivoSeleccionado" class="mt-1 text-xs font-medium text-slate-600">
                            <span x-text="archivoSeleccionado"></span>
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                        <button type="button" @click="cerrarModalImportacion()" :disabled="importandoInventario"
                            class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                            Cancelar
                        </button>
                        <button type="submit"
                            :disabled="importandoInventario || !archivoSeleccionado"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                            <span x-text="importandoInventario ? 'Importando...' : 'Importar CSV'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="importandoInventario" x-transition.opacity
            class="fixed inset-0 z-110 flex items-center justify-center bg-slate-900/70 p-4"
            role="status" aria-live="polite">
            <div class="w-full max-w-sm rounded-xl bg-white p-6 text-center shadow-2xl">
                <svg class="mx-auto h-12 w-12 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" stroke="currentColor" stroke-linecap="round"
                        stroke-width="4" d="M12 2a10 10 0 0 1 10 10"></path>
                </svg>
                <p class="mt-4 text-base font-semibold text-slate-900">Importando inventario...</p>
                <p class="mt-2 text-sm text-slate-600">
                    No cierres esta ventana ni vuelvas a enviar el archivo. Espera a que termine el proceso.
                </p>
            </div>
        </div>
    </template>
    </div>
</x-app-layout>
