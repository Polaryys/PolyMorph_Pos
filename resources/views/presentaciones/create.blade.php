<x-app-layout>
    <div x-data="{
        buscar: @js($productoSeleccionado['nombre'] ?? ''),
        inventarioId: @js($productoSeleccionado['inventario_id'] ?? ''),
        seleccionado: @js($productoSeleccionado),
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
        },

        cambiarProducto() {
            this.seleccionado = null;
            this.inventarioId = '';
            this.buscar = '';
            this.resultados = [];
        }
    }" class="relative space-y-6 ">
        <form action="{{ route('presentaciones.store') }}" method="POST">
            @csrf

            @include('presentaciones.partials._buscador-producto')

            @include('presentaciones.partials._formulario')
        </form>
    </div>

    <script>
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            document.getElementById('imagen_id').value = imagen.id;
            document.getElementById('imagen-preview').src = imagen.url;
        });
    </script>
</x-app-layout>
