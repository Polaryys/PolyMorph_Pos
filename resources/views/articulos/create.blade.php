<x-app-layout>

    <form action="{{ route('articulos.store') }}" method="POST">
        @csrf

        <!-- BOTONES -->
        <div class="mb-3 mt-1 flex justify-start space-x-3">

            <a href="{{ route('articulos.index') }}"
                class="rounded-md border border-gray-300 bg-white py-2 px-4
                       text-sm font-medium text-gray-700 shadow-sm
                       hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                       shadow-sm hover:bg-indigo-700 transition">
                Guardar Artículo
            </button>

        </div>

        <div class="space-y-8 divide-y divide-gray-200">

            <!-- datos del principales del artículo -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                    Crear Producto / Servicio
                </h3>
                <!-- IMAGEN DE LA PRESENTACIÓN BASE -->
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">
                        Imagen del artículo
                    </label>

                    <div class="mt-2 flex items-center gap-4">

                        <!-- Vista previa -->
                        <div class="relative h-24 w-24 shrink-0">
                            <img id="imagen-preview" src="{{ asset('images/default-avatar.png') }}"
                                alt="Imagen del artículo"
                                class="h-24 w-24 rounded-lg border border-gray-200 object-cover shadow-sm">

                            <!-- Abrir galería -->
                            <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                title="Seleccionar imagen de la galería" aria-label="Seleccionar imagen de la galería"
                                class="absolute -bottom-2 -right-2 rounded-full bg-indigo-600 p-2
                       text-white shadow-md transition hover:bg-indigo-700">

                                <x-lucide-camera class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="flex flex-col gap-1">
                            <p class="text-sm text-gray-600">
                                Selecciona una imagen para identificar el artículo.
                            </p>

                            <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                class="w-fit text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Elegir de la galería
                            </button>
                        </div>

                    </div>

                    <!-- Identificador de la imagen seleccionada -->
                    <input type="hidden" id="imagen_id" name="imagen_id" value="{{ old('imagen_id') }}">

                    @error('imagen_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <!-- tipo -->
                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="clasificacion" class="block text-sm font-medium text-gray-700">
                            Tipo <span class="text-red-500">*</span>
                        </label>

                        <select id="tipo" name="tipo"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                            @error('tipo') border-red-500 @enderror">

                            <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>
                                Seleccione un tipo
                            </option>

                            <option value="SERVICIO" {{ old('tipo') == 'SERVICIO' ? 'selected' : '' }}>
                                SERVICIO
                            </option>

                            <option value="PRODUCTO" {{ old('tipo') == 'PRODUCTO' ? 'selected' : '' }}>
                                PRODUCTO
                            </option>
                        </select>

                        @error('tipo')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror


                        @error('clasificacion')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Marca -->
                    <div class="sm:col-span-3">
                        <label for="marca_id" class="block text-sm font-medium text-gray-700">
                            Marca
                        </label>

                        <select id="marca_id" name="marca_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                            @error('marca_id') border-red-500 @enderror">

                            <option value="">Seleccionar marca</option>

                            @foreach ($marcas as $marca)
                                <option value="{{ $marca->id }}"
                                    {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach

                        </select>

                        @error('categoria_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- CATEGORÍA -->
                    <div class="sm:col-span-3">
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>

                        <select id="categoria_id" name="categoria_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                            @error('categoria_id') border-red-500 @enderror">

                            <option value="">Seleccionar categoría</option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach

                        </select>

                        @error('categoria_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- UMBRAL DE INVENTARIO -->
                    <div class="sm:col-span-3">
                        <label for="umbral" class="block text-sm font-medium text-gray-700">
                            Umbral de inventario <span class="text-red-500">*</span>
                        </label>

                        <input type="number" id="umbral" name="umbral" min="0" step="1"
                            value="{{ old('umbral', 0) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                            focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                            @error('umbral') border-red-500 @enderror">

                        @error('umbral')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- CÓDIGO SKU -->
                    <div class="sm:col-span-3">
                        <label for="codigoSKU" class="block text-sm font-medium text-gray-700">
                            Código SKU <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="codigoSKU" name="codigoSKU" maxlength="50"
                            value="{{ old('codigoSKU') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                @error('codigoSKU') border-red-500 @enderror">

                        @error('codigoSKU')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CÓDIGO DE BARRAS -->
                    <div class="sm:col-span-3">
                        <label for="codigoBarras" class="block text-sm font-medium text-gray-700">
                            Código de barras
                        </label>

                        <input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                            value="{{ old('codigoBarras') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                @error('codigoBarras') border-red-500 @enderror">

                        @error('codigoBarras')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- NOMBRE -->
                    <div class="sm:col-span-3">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nombre" name="nombre" maxlength="100"
                            value="{{ old('nombre') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                    @error('nombre') border-red-500 @enderror">

                        @error('nombre')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DETALLE -->
                    <div class="sm:col-span-6">
                        <label for="detalle" class="block text-sm font-medium text-gray-700">
                            Detalle
                        </label>

                        <textarea id="detalle" name="detalle" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                    focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                    @error('detalle') border-red-500 @enderror">{{ old('detalle') }}</textarea>

                        @error('detalle')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

    </form>

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
