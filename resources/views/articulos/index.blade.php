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
        {{-- Encabezado y acciones --}}
        <div class="mb-5 space-y-3">

            <div class="flex flex-col gap-3 xl:flex-row xl:items-center">

                {{-- Búsqueda --}}
                <div class="min-w-0 flex-1">
                    <x-search-bar :action="route('articulos.index')" :buscar="$buscar"
                        placeholder="Buscar artículos por nombre, código o marca">
                        <x-primary-link href="{{ route('articulos.create') }}" wire:navigate>
                            + Agregar Artículos
                        </x-primary-link>
                    </x-search-bar>
                </div>

                {{-- Filtros --}}
                <form action="{{ route('articulos.index') }}" method="GET" class="flex flex-col gap-2 sm:flex-row">
                    {{-- Conservar búsqueda --}}
                    <input type="hidden" name="buscar" value="{{ $buscar }}">

                    {{-- Categorías --}}
                    <div class="relative w-full sm:w-44">
                        <select name="categoria" onchange="this.form.submit()"
                            class="scrollbar-app w-full appearance-none rounded-md border border-app-primary-500
                            bg-transparent py-2 pl-3 pr-9 text-sm font-medium
                            text-app-primary-700 dark:text-app-primary-300
                            focus:border-app-primary-600 focus:outline-none
                            focus:ring-1 focus:ring-app-primary-500">
                            <option value="" class="bg-app-background text-app-text-primary">
                                Todas las categorías
                            </option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected((string) $categoriaId === (string) $categoria->id)
                                    class="bg-app-background text-app-text-primary">
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>

                        <x-lucide-chevron-down
                            class="pointer-events-none absolute right-3 top-1/2
                            h-4 w-4 -translate-y-1/2 text-app-primary-600
                            dark:text-app-primary-300" />
                    </div>

                    {{-- Marcas --}}
                    <div class="relative w-full sm:w-40">
                        <select name="marca" onchange="this.form.submit()"
                            class="scrollbar-app w-full appearance-none rounded-md border border-app-primary-500
                            bg-transparent py-2 pl-3 pr-9 text-sm font-medium
                            text-app-primary-500 dark:text-app-primary-300
                            focus:border-app-primary-400 focus:outline-none
                            focus:ring-1 focus:ring-app-primary-500">
                            <option value="" class="bg-app-background text-app-text-primary">
                                Todas las marcas
                            </option>

                            @foreach ($marcas as $marca)
                                <option value="{{ $marca->id }}" @selected((string) $marcaId === (string) $marca->id)
                                    class="bg-app-background text-app-text-primary">
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>

                        <x-lucide-chevron-down
                            class="pointer-events-none absolute right-3 top-1/2
                            h-4 w-4 -translate-y-1/2 text-app-primary-600
                            dark:text-app-primary-300" />
                    </div>

                </form>
            </div>
        </div>

        <!-- MENSAJES -->
        <x-alert-message />

        <!-- TABLA DE ARTÍCULOS -->
        <x-table :paginator="$articulos">

            <x-slot:headers>
                <th class="text-center w-16 whitespace-nowrap">Imagen</th>
                <th class="px-4 py-3 w-24 text-center whitespace-nowrap">SKU</th>
                <th class="px-4 py-3 w-14 text-left whitespace-nowrap">Tipo</th>
                <th class="px-4 py-3 text-left whitespace-nowrap">Nombre</th>
                <th class="px-4 py-3 text-center whitespace-nowrap">Marca</th>
                <th class="px-4 py-3 text-center whitespace-nowrap">Precio U.</th>
                <th class="px-4 py-3 w-16 text-center whitespace-nowrap">Cantidad</th>
                <th class="px-4 py-3 w-16 text-center whitespace-nowrap">Estado</th>
                <th class="px-4 py-3 w-16 text-center whitespace-nowrap">Acciones</th>
            </x-slot:headers>

            @forelse ($articulos as $articulo)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    @php $presentacionBase = $articulo->presentaciones->first() ?? null; @endphp
                    <!-- IMAGEN -->
                    <td class="px-2 py-2 text-center">
                        @if ($presentacionBase?->imagen)
                            <img src="{{ asset('storage/imagenes/' . $presentacionBase?->imagen->nombre_archivo) }}"
                                alt="{{ $presentacionBase?->nombre }}"
                                class="w-16 h-16 object-cover rounded border border-app-border mx-auto">
                        @else
                            <div
                                class="w-16 h-16 mx-auto flex items-center justify-center bg-app-background rounded text-slate-400">
                                <x-lucide-image class="w-7 h-7" />
                            </div>
                        @endif
                    </td>

                    <!-- SKU -->
                    <td class="px-4 py-3 font-medium text-center  whitespace-nowrap">
                        {{ $presentacionBase?->codigoSKU }}
                    </td>
                    <!-- TIPO -->
                    <td class="w-14 px-4 py-3 font-normal  whitespace-nowrap">
                        {{ $articulo?->tipo }}
                    </td>

                    <!-- NOMBRE -->
                    <td class="relative px-4 py-3">
                        <div class="flex min-h-10 items-center">
                            <span class="absolute inset-y-0 left-4 flex items-center font-normal">
                                {{ $presentacionBase?->nombre }}
                            </span>
                        </div>

                        <span class="block text-[10px] text-app-text-muted">
                            {{ $presentacionBase?->codigoBarras }}
                        </span>
                    </td>

                    <!-- MARCA -->
                    <td class="px-4 py-3 text-center font-medium  whitespace-nowrap">
                        {{ $articulo->marca?->nombre }}
                    </td>

                    <!-- PRECIO DE VENTA -->
                    <td class="px-4 py-3 text-center font-medium whitespace-nowrap">
                        Q {{ number_format((float) ($articulo->presentaciones->first()?->precioUnitario ?? 0), 2) }}
                    </td>

                    <!-- CANTIDAD -->
                    <td class="px-4 py-3 text-center font-medium whitespace-nowrap">
                        {{ $articulo->cantidad }}
                    </td>

                    <!-- ESTADO -->
                    <td class="px-4 py-3 text-center font-medium text-slate-900 whitespace-nowrap">
                        <span @class([
                            'inline-flex items-center rounded-full text-sm px-2.5 py-1 font-normal whitespace-nowrap',
                            'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' =>
                                $articulo->estado,
                            'bg-red-500/10 text-red-600 dark:text-red-400' => !$articulo->estado,
                        ])>
                            {{ $articulo->estado ? 'Activo' : 'Inactivo' }}
                        </span>
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
                class="inline-flex items-center justify-center rounded-md bg-app-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-app-primary-700">
                Importar inventario inicial
            </button>
        </div>


        <template x-teleport="body">
            {{-- Modal de importación --}}
            <div x-cloak x-show="modalImportacionAbierto" x-transition.opacity
                class="fixed inset-0 z-100 flex items-center justify-center
            bg-black/40 p-4"
                @click.self="cerrarModalImportacion()">

                <div x-show="modalImportacionAbierto" x-transition
                    class="w-full max-w-lg overflow-hidden rounded-xl border
                border-app-border bg-app-surface p-6 shadow-2xl"
                    role="dialog" aria-modal="true" aria-labelledby="titulo-modal-importacion" @click.stop>

                    {{-- Encabezado --}}
                    <h2 id="titulo-modal-importacion"
                        class="mb-4 border-b border-app-border pb-3
                    text-lg font-semibold text-app-text-primary">
                        Importar inventario inicial
                    </h2>

                    <form action="{{ route('articulos.importar-csv') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4" @submit="iniciarImportacion($event)">

                        @csrf
                        <input type="hidden" name="modal_importacion" value="1">

                        @if ($errors->has('archivo'))
                            <div class="rounded-md border border-red-300/50
                        bg-red-50 p-3 text-sm text-red-700
                        dark:border-red-800 dark:bg-red-950/30 dark:text-red-300"
                                role="alert">

                                <p class="font-medium">
                                    No se pudo importar el inventario:
                                </p>

                                <ul class="mt-1 list-inside list-disc">
                                    @foreach ($errors->get('archivo') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Descargar plantilla --}}
                        <div class="rounded-lg border border-app-border bg-app-surface-alt p-4">
                            <div class="flex items-start gap-3">

                                <div class="rounded-lg bg-app-primary-100 p-2 dark:bg-app-primary-900/40">
                                    <i data-lucide="file-down"
                                        class="h-5 w-5 text-app-primary-600 dark:text-app-primary-300">
                                    </i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-app-text-primary">
                                        ¿No tienes el archivo preparado?
                                    </p>

                                    <p class="mt-1 text-sm text-app-text-secondary">
                                        Descarga la plantilla con las columnas requeridas para
                                        completar los datos de tu inventario.
                                    </p>

                                    <a href="{{ route('inventario.plantilla.csv') }}"
                                        class="mt-3 inline-flex items-center gap-2 rounded-md
                                        border border-app-primary-500/30 bg-app-primary-50
                                        px-3 py-2 text-sm font-medium text-app-primary-700
                                        transition hover:bg-app-primary-100
                                        dark:bg-app-primary-900/20 dark:text-app-primary-300
                                        dark:hover:bg-app-primary-900/40">

                                        <i data-lucide="download" class="h-4 w-4"></i>

                                        Descargar plantilla CSV
                                    </a>
                                </div>

                            </div>
                        </div>

                        {{-- Archivo --}}
                        <div>
                            <label for="archivo-inventario"
                                class="mb-1 block text-sm font-medium
                            text-app-text-secondary">
                                Archivo CSV
                            </label>

                            <input x-ref="archivoInventario" type="file" id="archivo-inventario" name="archivo"
                                accept=".csv,text/csv" required
                                @change="archivoSeleccionado = $event.target.files.length
                            ? $event.target.files[0].name : ''"
                                class="block w-full rounded-md border
                            border-app-border-strong bg-app-background
                            text-sm text-app-text-secondary
                            file:mr-4 file:border-0
                            file:bg-app-primary-100 file:px-4 file:py-2.5
                            file:font-medium file:text-app-primary-700
                            hover:file:bg-app-primary-200
                            focus:border-app-primary-500 focus:outline-none
                            focus:ring-1 focus:ring-app-primary-500
                            dark:file:bg-app-primary-900/40
                            dark:file:text-app-primary-200
                            dark:hover:file:bg-app-primary-900/60">

                            <p class="mt-2 text-xs text-app-text-muted">
                                Sube el CSV de inventario con las columnas esperadas.
                                Tamaño máximo: 10 MB.
                            </p>

                            <p x-show="archivoSeleccionado" class="mt-1 text-xs font-medium text-app-text-secondary">
                                <span x-text="archivoSeleccionado"></span>
                            </p>
                        </div>

                        {{-- Acciones --}}
                        <div
                            class="flex items-center justify-end gap-3
                    border-t border-app-border pt-4">

                            <button type="button" @click="cerrarModalImportacion()" :disabled="importandoInventario"
                                class="rounded-md border border-app-border-strong
                            px-4 py-2 text-sm font-medium
                            text-app-text-secondary transition
                            hover:bg-app-surface-hover
                            disabled:cursor-not-allowed disabled:opacity-50">
                                Cancelar
                            </button>

                            <button type="submit" :disabled="importandoInventario || !archivoSeleccionado"
                                class="rounded-md bg-app-primary-600 px-4 py-2
                            text-sm font-medium text-white shadow-sm
                            transition hover:bg-app-primary-700
                            focus:outline-none focus:ring-2
                            focus:ring-app-primary-500 focus:ring-offset-2
                            focus:ring-offset-app-surface
                            disabled:cursor-not-allowed disabled:opacity-50">
                                <span
                                    x-text="importandoInventario
                            ? 'Importando...' : 'Importar CSV'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Indicador de importación --}}
            <div x-cloak x-show="importandoInventario" x-transition.opacity
                class="fixed inset-0 z-110 flex items-center justify-center
            bg-black/60 p-4"
                role="status" aria-live="polite">

                <div
                    class="w-full max-w-sm rounded-xl border
            border-app-border bg-app-surface p-6 text-center shadow-2xl">

                    <svg class="mx-auto h-12 w-12 animate-spin text-app-primary-600"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">

                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>

                        <path class="opacity-75" stroke="currentColor" stroke-linecap="round" stroke-width="4"
                            d="M12 2a10 10 0 0 1 10 10"></path>
                    </svg>

                    <p class="mt-4 text-base font-semibold text-app-text-primary">
                        Importando inventario...
                    </p>

                    <p class="mt-2 text-sm text-app-text-secondary">
                        No cierres esta ventana ni vuelvas a enviar el archivo.
                        Espera a que termine el proceso.
                    </p>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
