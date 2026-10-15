
<div
    x-data="selectorImagenes()"
    @abrir-selector-imagen.window="abrir()"
    @keydown.escape.window="cerrar()"
>
    {{-- modal --}}
    <div
        x-cloak
        x-show="abierto"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4"
        @click.self="cerrar()"
    >
        <div
            x-show="abierto"
            x-transition
            role="dialog"
            aria-modal="true"
            aria-labelledby="titulo-selector-imagen"
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h2 id="titulo-selector-imagen" class="text-base font-semibold text-gray-900">
                    Seleccionar imagen
                </h2>

                <button
                    type="button"
                    @click="cerrar()"
                    aria-label="Cerrar"
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- buscar --}}
            <form
                class="flex gap-2 border-b border-gray-200 p-4"
                @submit.prevent="buscar()"
            >
                <input
                    type="search"
                    x-model="termino"
                    placeholder="Buscar imagen por nombre..."
                    class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                >
                    Buscar
                </button>
            </form>

            {{-- carga de images --}}
            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                <p x-show="cargando" class="py-8 text-center text-sm text-gray-500">
                    Cargando imágenes...
                </p>

                <p x-show="error" x-text="error"
                    class="py-8 text-center text-sm text-red-600"></p>

                <div
                    x-show="!cargando && !error && imagenes.length === 0"
                    class="py-10 text-center text-sm text-gray-500"
                >
                    No se encontraron imágenes.
                </div>

                <div
                    x-show="!cargando && imagenes.length > 0"
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"
                >
                    <template x-for="imagen in imagenes" :key="imagen.id">
                        <button
                            type="button"
                            @click="seleccionar(imagen)"
                            class="group min-w-0 overflow-hidden rounded-lg border border-gray-200 text-left transition hover:border-indigo-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <div class="flex aspect-square items-center justify-center overflow-hidden bg-gray-50">
                                <img
                                    :src="imagen.url"
                                    :alt="imagen.nombre"
                                    loading="lazy"
                                    class="h-full w-full object-contain"
                                >
                            </div>

                            <div class="border-t border-gray-100 p-2.5">
                                <p
                                    class="truncate text-sm font-medium text-gray-800 group-hover:text-indigo-700"
                                    x-text="imagen.nombre"
                                    :title="imagen.nombre"
                                ></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- paginar resultados --}}
            <div class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
                <button
                    type="button"
                    @click="cambiarPagina(pagina - 1)"
                    :disabled="pagina <= 1 || cargando"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Anterior
                </button>

                <span class="text-xs text-gray-500">
                    Página <span x-text="pagina"></span>
                    de <span x-text="ultimaPagina"></span>
                </span>

                <button
                    type="button"
                    @click="cambiarPagina(pagina + 1)"
                    :disabled="pagina >= ultimaPagina || cargando"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Siguiente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function selectorImagenes() {
        return {
            abierto: false,
            cargando: false,
            error: '',
            termino: '',
            imagenes: [],
            pagina: 1,
            ultimaPagina: 1,

            async abrir() {
                this.abierto = true;
                this.termino = '';
                this.pagina = 1;
                await this.cargar();
            },

            cerrar() {
                this.abierto = false;
            },

            async buscar() {
                this.pagina = 1;
                await this.cargar();
            },

            async cambiarPagina(numero) {
                if (numero < 1 || numero > this.ultimaPagina) return;
                this.pagina = numero;
                await this.cargar();
            },

            async cargar() {
                this.cargando = true;
                this.error = '';

                try {
                    const parametros = new URLSearchParams({
                        buscar: this.termino,
                        page: this.pagina
                    });

                    const respuesta = await fetch(
                        @json(route('imagenes.selector')) + '?' + parametros.toString(),
                        {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!respuesta.ok) {
                        throw new Error('No se pudieron cargar las imágenes.');
                    }

                    const datos = await respuesta.json();

                    this.imagenes = datos.data;
                    this.pagina = datos.current_page;
                    this.ultimaPagina = datos.last_page;
                } catch (e) {
                    this.imagenes = [];
                    this.error = e.message || 'Ocurrió un error al cargar las imágenes.';
                } finally {
                    this.cargando = false;
                }
            },

            seleccionar(imagen) {
                window.dispatchEvent(
                    new CustomEvent('imagen-seleccionada', {
                        detail: imagen
                    })
                );

                this.cerrar();
            }
        };
    }
</script>
