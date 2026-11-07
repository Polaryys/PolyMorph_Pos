@props([
    'action',
    'productoSeleccionado' => null,
    'name' => 'inventario_id',
    'label' => 'Producto base',
    'placeholder' => 'Buscar por nombre, SKU o código de barras...',
])

<div x-data="{
    buscar: @js(data_get($productoSeleccionado, 'nombre', '')),
    inventarioId: @js(data_get($productoSeleccionado, 'inventario_id', '')),
    seleccionado: @js($productoSeleccionado),
    resultados: [],
    cargando: false,
    errorBusqueda: false,
    versionBusqueda: 0,

    async buscarProductos() {
        const version = ++this.versionBusqueda;
        const termino = this.buscar.trim();

        if (!termino) {
            this.resultados = [];
            this.cargando = false;
            return;
        }

        this.cargando = true;
        this.errorBusqueda = false;

        try {
            const url = new URL(@js($action), window.location.origin);
            url.searchParams.set('buscar', termino);

            const respuesta = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!respuesta.ok) {
                throw new Error('No se pudo realizar la búsqueda.');
            }

            const resultados = await respuesta.json();
            if (version === this.versionBusqueda) {
                this.resultados = resultados;
            }

        } catch (error) {
            if (version === this.versionBusqueda) {
                this.resultados = [];
                this.errorBusqueda = true;
            }

        } finally {
            if (version === this.versionBusqueda) {
                this.cargando = false;
            }
        }
    },

    seleccionar(producto) {
        this.versionBusqueda++;
        this.seleccionado = producto;
        this.inventarioId = producto.inventario_id;
        this.buscar = producto.nombre;
        this.resultados = [];
        this.cargando = false;

        this.$dispatch('producto-seleccionado', {
            producto: producto
        });
    },

    cambiarProducto() {
        this.versionBusqueda++;
        this.seleccionado = null;
        this.inventarioId = '';
        this.buscar = '';
        this.resultados = [];
        this.cargando = false;

        this.$dispatch('producto-deseleccionado');
    }
}" {{ $attributes->merge(['class' => 'relative space-y-2']) }}>
    {{-- Etiqueta --}}
    <x-input-label :value="$label" />

    {{-- Campo de búsqueda --}}
    <div class="flex items-center gap-2">
        <x-lucide-search class="w-5 h-5 shrink-0 text-slate-400" />
        <x-text-input type="search" x-model="buscar" @input="versionBusqueda++; inventarioId = ''; seleccionado = null"
            @input.debounce.300ms="buscarProductos()" x-bind:disabled="seleccionado !== null"
            placeholder="{{ $placeholder }}" class="w-96" />

        <div x-show="cargando" x-cloak>
            <x-lucide-loader-circle class="w-5 h-5 animate-spin text-indigo-600" />
        </div>

    </div>

    {{-- Campo oculto que se envía al controlador --}}
    <input type="hidden" name="{{ $name }}" x-model="inventarioId">

    {{-- Resultados --}}
    <div x-show="resultados.length > 0" x-cloak
        class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg">
        <template x-for="producto in resultados" :key="producto.inventario_id">
            <button type="button" @click="seleccionar(producto)"
                class="flex w-full items-center gap-3 border-b border-slate-100 p-3 text-left transition hover:bg-slate-50">
                {{-- Imagen --}}
                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-slate-100">
                    <template x-if="producto.imagen || producto.imagen_url">
                        <img :src="producto.imagen || producto.imagen_url" :alt="producto.nombre"
                            class="h-full w-full object-cover">
                    </template>

                    <template x-if="!producto.imagen">
                        <div class="flex h-full w-full items-center justify-center">
                            <x-lucide-image class="h-6 w-6 text-slate-400" />
                        </div>
                    </template>
                </div>

                {{-- Información --}}
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800"
                        x-text="producto.marcaNombre ? `${producto.nombre} - ${producto.marcaNombre}` : producto.nombre">
                    </p>

                    <p class="text-xs text-slate-500">
                        SKU: <span x-text="producto.sku"></span>
                    </p>

                    <p class="text-xs text-slate-500">
                        Existencias: <span x-text="producto.cantidad"></span>
                    </p>
                </div>

                <x-lucide-chevron-right class="h-5 w-5 shrink-0 text-slate-400" />
            </button>
        </template>
    </div>

    {{-- Mensajes de búsqueda --}}
    <p x-show="errorBusqueda" x-cloak class="text-sm text-red-600">
        No se pudo realizar la búsqueda. Inténtalo nuevamente.
    </p>

    <p x-show="buscar.trim().length > 0 && resultados.length === 0 && !cargando && !errorBusqueda && seleccionado === null"
        x-cloak class="text-sm text-slate-500">
        No se encontraron productos.
    </p>

    {{-- Producto seleccionado --}}
    <div x-show="seleccionado !== null" x-cloak
        class="flex items-center justify-between gap-3 rounded-lg border border-indigo-200 bg-indigo-50 p-2">

        <div class="flex min-w-0 items-center gap-3">

            {{-- Imagen del producto --}}
            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-indigo-100 bg-white">

                <template x-if="seleccionado?.imagen || seleccionado?.imagen_url">
                    <img :src="seleccionado.imagen || seleccionado.imagen_url" :alt="seleccionado.nombre"
                        class="h-full w-full object-cover">
                </template>

                <template x-if="!seleccionado?.imagen && !seleccionado?.imagen_url">
                    <div class="flex h-full w-full items-center justify-center">
                        <x-lucide-image class="h-8 w-8 text-slate-400" />
                    </div>
                </template>

            </div>

            {{-- Información --}}
            <div class="min-w-0">
                <p class="text-xs font-medium text-indigo-600">
                    Producto seleccionado
                </p>

                <p class="truncate text-sm font-semibold text-slate-800"
                    x-text="seleccionado?.marcaNombre
                        ? `${seleccionado.nombre} - ${seleccionado.marcaNombre}`
                        : seleccionado?.nombre">
                </p>

                <p class="text-xs text-slate-500">
                    SKU: <span x-text="seleccionado?.sku"></span>
                </p>

                <p class="text-xs text-slate-500">
                    Existencias: <span x-text="seleccionado?.cantidad"></span>
                </p>
            </div>

        </div>

        <button type="button" @click="cambiarProducto()"
            class="shrink-0 rounded-md px-3 py-1.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">
            Cambiar
        </button>

    </div>

    {{-- Validación --}}
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
