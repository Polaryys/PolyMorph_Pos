<x-app-layout>
    <form action="{{ route('articulos.store') }}" method="POST">
        @csrf

        <div class="mb-4 mt-1 flex justify-start gap-3">
            <a href="{{ route('articulos.index') }}"
                class="rounded-md border border-gray-300 bg-white px-4 py-2
                       text-sm font-medium text-gray-700 shadow-sm
                       transition hover:bg-gray-50">
                Volver
            </a>

            <button type="submit"
                class="inline-flex justify-center rounded-md border
                       border-transparent bg-indigo-600 px-4 py-2
                       text-sm font-medium text-white shadow-sm
                       transition hover:bg-indigo-700">
                Guardar artículo
            </button>
        </div>

        <div class="space-y-6">
            <h3 class="flex items-center gap-2 text-lg font-semibold
                       leading-6 text-indigo-700">
                Crear producto / servicio
            </h3>

            <!-- imagen y datos inciales -->
            <div class="grid grid-cols-1 items-start gap-6
                        md:grid-cols-[180px_minmax(0,1fr)]">

                @include('articulos.partials._imagen')

                @include('articulos.partials._datos-principales')
            </div>
            

            <div class="border-t border-gray-200 pt-6 space-y-6">

                <!-- campos de productos -->
                @include('articulos.partials._campos-producto')

                <!-- campos de servicios -->
                @include('articulos.partials._campos-servicio')

                @include('articulos.partials._detalle')

            </div>
        </div>
    </form>

    <!-- Selector de imágenes -->
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

    <!-- campos según tipo -->
    <script>
        const tipoSelect = document.getElementById('tipo');
        const camposProducto = document.getElementById('campos-producto');
        const camposServicio = document.getElementById('campos-servicio');
        const umbralInput = document.getElementById('umbral');
        const precioServicioInput = document.getElementById('precioUnitario');

        function actualizarCampos() {
            const tipo = tipoSelect.value;
            const esProducto = tipo === 'PRODUCTO';
            const esServicio = tipo === 'SERVICIO';

            camposProducto.style.display = esProducto ? 'grid' : 'none';
            camposServicio.style.display = esServicio ? 'grid' : 'none';

            umbralInput.required = esProducto;
            precioServicioInput.required = esServicio;
        }

        tipoSelect.addEventListener('change', actualizarCampos);

        actualizarCampos();
    </script>
</x-app-layout>
