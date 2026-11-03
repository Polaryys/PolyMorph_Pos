<x-app-layout>

    <div x-data="{ productoSeleccionado: false }" @producto-seleccionado.window="productoSeleccionado = true"
        @producto-deseleccionado.window="productoSeleccionado = false">

        <form action="{{ route('presentaciones.store') }}" method="POST">
            @csrf

            <x-product-search :action="route('presentaciones.buscar-productos')" :producto-seleccionado="$productoSeleccionado ?? null" name="inventario_id" label="Producto base"
                placeholder="Buscar por nombre, SKU o código de barras..." />

            <div x-cloak x-show="productoSeleccionado" x-transition>
                @include('presentaciones.partials._formulario')
            </div>

        </form>

    </div>

    <script>
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            const inputImagen = document.getElementById('imagen_id');
            const vistaPrevia = document.getElementById('imagen-preview');

            inputImagen.value = imagen.id;
            vistaPrevia.src = imagen.url;
            vistaPrevia.alt = imagen.nombre || 'Imagen de la presentación';
            vistaPrevia.classList.remove('hidden');
            document.getElementById('imagen-placeholder').classList.add('hidden');
        });
    </script>

</x-app-layout>
