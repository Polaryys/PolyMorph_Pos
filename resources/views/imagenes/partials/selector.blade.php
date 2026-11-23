<div x-data="selectorImagenes()" @abrir-selector-imagen.window="abrir()" @keydown.escape.window="cerrar()">
    {{-- modal --}}
    <div x-cloak x-show="abierto" x-transition.opacity
        class="fixed inset-0 z-100 flex items-center justify-center bg-black/50 p-4" @click.self="cerrar()">
        <div x-show="abierto" x-transition role="dialog" aria-modal="true" aria-labelledby="titulo-selector-imagen"
            class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl border border-app-border bg-app-surface shadow-xl">
            <div class="flex items-center justify-between border-b border-app-border px-5 py-4">
                <h2 id="titulo-selector-imagen" class="text-base font-semibold text-app-text-primary">
                    Seleccionar imagen
                </h2>

                <button type="button" @click="cerrar()" aria-label="Cerrar"
                    class="rounded-lg p-2 text-app-text-muted transition hover:bg-app-surface-hover hover:text-app-text-primary">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- buscar --}}
            <form class="flex gap-2 border-b border-app-border p-4" @submit.prevent="buscar()">
                <input type="search" x-model="termino" placeholder="Buscar imagen por nombre..."
                    class="min-w-0 flex-1 pl-2 rounded-lg border border-app-border-strong bg-app-background text-sm text-app-text-primary shadow-sm placeholder:text-app-text-muted focus:border-app-primary-500 focus:ring-app-primary-500">

                <button type="submit"
                    class="rounded-lg bg-app-primary-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-app-primary-700">
                    Buscar
                </button>
            </form>

            {{-- carga de images --}}
            <div class="min-h-0 flex-1 overflow-y-auto scrollbar-app p-4">
                <p x-show="cargando" class="py-8 text-center text-sm text-app-text-muted">
                    Cargando imágenes...
                </p>

                <p x-show="error" x-text="error" class="py-8 text-center text-sm text-red-600 dark:text-red-400"></p>

                <div x-show="!cargando && !error && imagenes.length === 0"
                    class="py-10 text-center text-sm text-app-text-muted">
                    No se encontraron imágenes.
                </div>

                <div x-show="!cargando && imagenes.length > 0"
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    <template x-for="imagen in imagenes" :key="imagen.id">
                        <button type="button"  @click="seleccionar(imagen)"
                            class="group min-w-0 overflow-hidden rounded-lg border border-app-border bg-app-surface text-left transition hover:border-app-primary-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-app-primary-500">
                            <div
                                class="flex aspect-square items-center justify-center overflow-hidden bg-app-surface-alt">
                                <img :src="imagen.url" :alt="imagen.nombre" loading="lazy"
                                    class="h-full w-full object-contain">
                            </div>

                            <div class="border-t border-app-border p-2.5">
                                <p class="truncate text-sm font-medium text-app-text-secondary group-hover:text-app-primary-600"
                                    x-text="imagen.nombre" :title="imagen.nombre"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- paginar resultados --}}
            <div class="flex items-center justify-between border-t border-app-border bg-app-surface-alt/50 px-4 py-3">
                <button type="button" @click="cambiarPagina(pagina - 1)" :disabled="pagina <= 1 || cargando"
                    class="rounded-lg border border-app-border-strong bg-app-surface px-3 py-2 text-sm font-medium text-app-text-secondary transition hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
                    Anterior
                </button>

                <span class="text-xs text-app-text-muted">
                    Página <span x-text="pagina"></span>
                    de <span x-text="ultimaPagina"></span>
                </span>

                <button type="button" @click="cambiarPagina(pagina + 1)"
                    :disabled="pagina >= ultimaPagina || cargando"
                    class="rounded-lg border border-app-border-strong bg-app-surface px-3 py-2 text-sm font-medium text-app-text-secondary transition hover:bg-app-surface-hover disabled:cursor-not-allowed disabled:opacity-40">
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
                        @json(route('imagenes.selector')) + '?' + parametros.toString(), {
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
