<x-app-layout>
    <form action="{{ route('articulos.store') }}" method="POST">
        @csrf



        <div class=" mt-2 space-y-6" id="formulario-articulo">

            <!-- imagen y datos inciales -->
            <div class="grid grid-cols-1 items-start gap-6
                        md:grid-cols-[220px_minmax(0,1fr)]">

                @include('articulos.partials._imagen')

                @include('articulos.partials._datos-principales')
            </div>


            <div class=" space-y-6">

                <!-- campos de productos -->
                @include('articulos.partials._campos-producto')

                <!-- campos de servicios -->
                @include('articulos.partials._campos-servicio')

                <div>
                    <x-input-label for="detalle">
                        Descripción o detalle
                    </x-input-label>

                    <textarea id="detalle" name="detalle" rows="3" placeholder="Agrega características o información adicional..."
                        class="mt-1 block w-full rounded-md border border-gray-300
                p-2 text-sm shadow-sm focus:border-indigo-500
                focus:border-indigo-500
                focus:ring-indigo-500
                focus:ring-indigo-500 @error('detalle') border-red-500 @enderror">{{ old('detalle') }}</textarea>

                    <x-input-error :messages="$errors->get('detalle')" />
                </div>

            </div>
        </div>
        <div class="mb-6 mt-5 flex justify-end gap-3">
            <x-secondary-link href="{{ route('articulos.index') }}">
                Volver
            </x-secondary-link>

            <x-primary-button>
                Crear Artículo
            </x-primary-button>
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

        const formularioArticulo = document.getElementById('formulario-articulo');

        function actualizarCampos() {
            const tipo = tipoSelect.value;
            const esProducto = tipo === 'PRODUCTO';
            const esServicio = tipo === 'SERVICIO';
            const tipoSeleccionado = esProducto || esServicio;

            camposProducto.style.display = esProducto ? 'grid' : 'none';
            camposServicio.style.display = esServicio ? 'grid' : 'none';

            formularioArticulo.querySelectorAll('input, select, textarea').forEach(campo => {
                if (campo.id !== 'tipo') {
                    campo.disabled = !tipoSeleccionado;
                }
            });

            umbralInput.required = esProducto;
            precioServicioInput.required = esServicio;
        }

        tipoSelect.addEventListener('change', actualizarCampos);

        actualizarCampos();
    </script>
</x-app-layout>
