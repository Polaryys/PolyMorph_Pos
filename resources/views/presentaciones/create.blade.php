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

            document.getElementById('imagen_id').value = imagen.id;
            document.getElementById('imagen-preview').src = imagen.url;
        });
    </script>

</x-app-layout>
