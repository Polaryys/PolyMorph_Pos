<x-app-layout>

    <div x-data="{
        productoSeleccionado: @js($productoSeleccionado !== null),
        productoSeleccionadoData: @js($productoSeleccionado),
        factorPresentacion: @js((int) old('factor', 1)),
    }"
        @producto-seleccionado.window="productoSeleccionado = true; productoSeleccionadoData = $event.detail.producto"
        @producto-deseleccionado.window="productoSeleccionado = false; productoSeleccionadoData = null">

        <form action="{{ route('presentaciones.store') }}" method="POST">
            @csrf

            <x-product-search :action="route('presentaciones.buscar-productos')" :producto-seleccionado="$productoSeleccionado ?? null" name="inventario_id" label="Producto base"
                placeholder="Buscar por nombre, SKU o código de barras..." />

            <div x-cloak x-show="productoSeleccionadoData"
                class="mt-3 grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm sm:grid-cols-2">
                <p class="text-slate-600">
                    Precio unitario:
                    <strong class="text-slate-800"
                        x-text="'Q ' + Number(productoSeleccionadoData?.precioUnitario ?? 0).toFixed(2)"></strong>
                </p>
                <p class="text-slate-600">
                    Costo promedio unitario:
                    <strong class="text-slate-800"
                        x-text="'Q ' + Number(productoSeleccionadoData?.costoPromedioUnitario ?? 0).toFixed(2)"></strong>
                </p>
            </div>

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
