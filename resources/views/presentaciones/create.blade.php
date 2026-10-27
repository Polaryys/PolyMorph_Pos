<x-app-layout>


    <div x-data="{
        buscar: '',
        inventarioId: '',
        seleccionado: null,
        resultados: [],
        cargando: false,
        errorBusqueda: false,
    
        async buscarProductos() {
            const termino = this.buscar.trim();
    
            if (!termino) {
                this.resultados = [];
                this.cargando = false;
                return;
            }
    
            this.cargando = true;
            this.errorBusqueda = false;
    
            try {
                const respuesta = await fetch(
                    '{{ route('presentaciones.buscar-productos') }}?buscar=' +
                    encodeURIComponent(termino), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );
    
                if (!respuesta.ok) {
                    throw new Error('No se pudo realizar la búsqueda.');
                }
    
                this.resultados = await respuesta.json();
            } catch (error) {
                this.resultados = [];
                this.errorBusqueda = true;
            } finally {
                this.cargando = false;
            }
        },
    
        seleccionar(producto) {
            this.seleccionado = producto;
            this.inventarioId = producto.inventario_id;
            this.buscar = producto.nombre;
            this.resultados = [];
        }
    }" class="relative">
        <label class="mb-1 block text-sm font-medium text-slate-700">
            Producto base <span class="text-red-500">*</span>
        </label>

        <input type="hidden" name="inventario_id" :value="inventarioId">

        <div class="relative">
            <input type="text" x-model="buscar" @input="inventarioId = ''; seleccionado = null"
                @input.debounce.300ms="buscarProductos()" :disabled="seleccionado !== null"
                placeholder="Buscar por nombre, SKU o código de barras..." autocomplete="off" required
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500">

            <div class="absolute inset-y-0 right-3 flex items-center">
                <svg x-show="cargando" class="h-4 w-4 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                </svg>
            </div>
        </div>

        <!-- Resultados de Laravel -->
        <div x-cloak x-show="buscar.trim().length > 0 && !seleccionado"
            class="absolute z-50 mt-1 max-h-80 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
            <div x-show="cargando && resultados.length === 0" class="px-4 py-3 text-sm text-slate-500">
                Buscando productos...
            </div>

            <template x-for="(producto, indice) in resultados" :key="producto.inventario_id">
                <button type="button" @click="seleccionar(producto)"
                    class="flex w-full items-center gap-3 border-b border-slate-100 px-3 py-3 text-left transition hover:bg-indigo-50">
                    <!-- Imagen al inicio -->
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                        <template x-if="producto.imagen">
                            <img :src="producto.imagen" :alt="producto.nombre" class="h-full w-full object-cover">
                        </template>

                        <template x-if="!producto.imagen">
                            <svg class="h-6 w-6 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5A1.5 1.5 0 014.5 6h15A1.5 1.5 0 0121 7.5v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 16.5v-9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 15 5-5 4 4 3-3 6 6" />
                            </svg>
                        </template>
                    </div>

                    <!-- Datos del producto -->
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800" x-text="producto.nombre"></p>

                        <p class="mt-1 text-xs text-slate-500">
                            SKU:
                            <span x-text="producto.sku"></span>
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Existencias:
                            <span class="font-semibold"
                                :class="producto.cantidad > 0 ? 'text-emerald-700' : 'text-red-600'"
                                x-text="producto.cantidad"></span>
                        </p>

                        <p x-show="producto.codigoBarras" class="mt-0.5 text-xs text-slate-500">
                            Código de barras:
                            <span x-text="producto.codigoBarras"></span>
                        </p>
                    </div>

                    <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                    </svg>
                </button>
            </template>

            <p x-show="!cargando && !errorBusqueda && buscar.trim().length > 0 && resultados.length === 0"
                class="px-4 py-4 text-sm text-slate-500">
                No se encontraron productos coincidentes.
            </p>

            <p x-show="errorBusqueda" class="px-4 py-4 text-sm text-red-600">
                No se pudo completar la búsqueda. Intenta nuevamente.
            </p>
        </div>

        <!-- Producto seleccionado -->
        <div x-cloak x-show="seleccionado"
            class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-indigo-200 bg-indigo-50 p-3">
            <div class="flex min-w-0 items-center gap-3">
                <template x-if="seleccionado?.imagen">
                    <img :src="seleccionado.imagen" :alt="seleccionado.nombre"
                        class="h-12 w-12 shrink-0 rounded-lg border border-indigo-100 bg-white object-cover">
                </template>

                <div class="min-w-0">
                    <p class="text-xs font-medium text-indigo-600">
                        Producto seleccionado
                    </p>
                    <p class="truncate text-sm font-semibold text-slate-800" x-text="seleccionado?.nombre"></p>
                    <p class="text-xs text-slate-500" x-text="'SKU: ' + (seleccionado?.sku ?? '')"></p>
                </div>
            </div>

            <button type="button" @click="buscar = ''; inventarioId = ''; seleccionado = null; resultados = []"
                class="shrink-0 rounded-lg px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                Cambiar
            </button>
        </div>

        @error('inventario_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</x-app-layout>
