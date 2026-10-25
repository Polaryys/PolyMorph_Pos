<x-app-layout>
    @php
        $presentacion = $articulo->presentaciones->firstWhere('base', true) ?? $articulo->presentaciones->first();
    @endphp

    <div class="space-y-6">

        {{-- Encabezado --}}
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Editar {{ $articulo->tipo === 'PRODUCTO' ? 'producto' : 'servicio' }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Modifica la información del artículo y guarda los cambios.
            </p>
        </div>

        <form action="{{ route('articulos.update', $articulo->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Información general --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Información general
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-x-4 gap-y-5 p-5 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Tipo: no editable --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Tipo de artículo
                        </label>

                        <div class="mt-1.5">
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $articulo->tipo === 'PRODUCTO' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $articulo->tipo }}
                            </span>
                        </div>
                    </div>

                    {{-- Categoría --}}
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>

                        <select id="categoria_id" name="categoria_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sin categoría</option>

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $articulo->categoria_id) == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('categoria_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Marca --}}
                    <div>
                        <label for="marca_id" class="block text-sm font-medium text-gray-700">
                            Marca
                        </label>

                        <select id="marca_id" name="marca_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sin marca</option>

                            @foreach ($marcas as $marca)
                                <option value="{{ $marca->id }}" @selected(old('marca_id', $articulo->marca_id) == $marca->id)>
                                    {{ $marca->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('marca_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700">
                            Estado del artículo <span class="text-red-500">*</span>
                        </label>

                        <select id="estado" name="estado" required
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" @selected((string) old('estado', (int) $articulo->estado) === '1')>
                                Activo
                            </option>

                            <option value="0" @selected((string) old('estado', (int) $articulo->estado) === '0')>
                                Inactivo
                            </option>
                        </select>

                        @error('estado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Umbral --}}
                    <div>
                        <label for="umbral" class="block text-sm font-medium text-gray-700">
                            Umbral de inventario <span class="text-red-500">*</span>
                        </label>

                        <input type="number" id="umbral" name="umbral" min="0" step="1" required
                            value="{{ old('umbral', $articulo->umbral ?? 0) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('umbral')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Datos de la presentación base --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Datos de la presentación base
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-x-4 gap-y-5 p-5 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Nombre --}}
                    <div class="sm:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nombre" name="nombre" maxlength="100" required
                            value="{{ old('nombre', $presentacion?->nombre) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SKU --}}
                    <div>
                        <label for="codigoSKU" class="block text-sm font-medium text-gray-700">
                            Código SKU <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                            value="{{ old('codigoSKU', $presentacion?->codigoSKU) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('codigoSKU')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Código de barras --}}
                    <div>
                        <label for="codigoBarras" class="block text-sm font-medium text-gray-700">
                            Código de barras
                        </label>

                        <input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                            value="{{ old('codigoBarras', $presentacion?->codigoBarras) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('codigoBarras')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700">
                            Ubicación física
                        </label>

                        <input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                            value="{{ old('ubicacion', $presentacion?->ubicacion) }}" placeholder="Ej. Estante A-1"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('ubicacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de vencimiento --}}
                    <div>
                        <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">
                            Fecha de vencimiento
                        </label>

                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                            value="{{ old(
                                'fecha_vencimiento',
                                $presentacion?->fecha_vencimiento ? \Carbon\Carbon::parse($presentacion->fecha_vencimiento)->format('Y-m-d') : '',
                            ) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500">

                        @error('fecha_vencimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Detalle --}}
                    <div class="sm:col-span-2 xl:col-span-4">
                        <label for="detalle" class="block text-sm font-medium text-gray-700">
                            Descripción o detalle
                        </label>

                        <textarea id="detalle" name="detalle" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                         focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Descripción adicional del artículo...">{{ old('detalle', $presentacion?->detalle) }}</textarea>

                        @error('detalle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Imagen --}}
                    <div class="sm:col-span-2 xl:col-span-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Imagen del artículo
                        </label>

                        <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center">

                            {{-- Vista previa --}}
                            <div class="relative h-32 w-32 shrink-0">
                                <img id="imagen-preview"
                                    src="{{ $presentacion?->imagen
                                        ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                                        : asset('images/default-avatar.png') }}"
                                    alt="Imagen del artículo"
                                    class="h-32 w-32 rounded-lg border-2 border-indigo-100 bg-gray-50 object-cover shadow-sm">

                                {{-- Abrir galería --}}
                                <button type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                    title="Seleccionar imagen" aria-label="Seleccionar imagen"
                                    class="absolute bottom-0 right-0 rounded-full bg-indigo-600 p-2 text-white shadow-md transition hover:bg-indigo-700">

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Campo que guarda la imagen seleccionada --}}
                            <div class="space-y-2">
                                <input type="hidden" id="imagen_id" name="imagen_id"
                                    value="{{ old('imagen_id', $presentacion?->imagen_id) }}">

                                <button type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                    class="inline-flex items-center rounded-lg border border-slate-300
                                        bg-white px-4 py-2 text-sm font-medium text-gray-700
                                        shadow-sm transition hover:bg-slate-50">
                                    Seleccionar imagen
                                </button>

                                <p class="text-xs text-gray-500">
                                    Selecciona una imagen de la biblioteca del sistema.
                                </p>

                                @error('imagen_id')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Información de inventario --}}
            @if ($articulo->tipo === 'PRODUCTO')
                <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                            Información de inventario
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Datos informativos administrados mediante los ingresos de inventario.
                        </p>
                    </div>

                    <div class="p-5">

                        {{-- Existencias y costo --}}
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-left">

                                <thead
                                    class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Existencias actuales</th>
                                        <th class="px-4 py-3">Costo promedio unitario</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                            {{ number_format($articulo->cantidad ?? 0, 0) }} unidades
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-gray-800">
                                            Q {{ number_format($articulo->costoPromedioUnitario ?? 0, 2) }}
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            @endif

            {{-- Precios de venta --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Precios de venta
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $articulo->tipo === 'PRODUCTO'
                            ? 'Puedes actualizar los precios de venta sin modificar las existencias ni el costo promedio.'
                            : 'Los servicios únicamente manejan un precio de venta unitario.' }}
                    </p>
                </div>

                <div class="p-5">

                    @if ($articulo->tipo === 'PRODUCTO')
                        {{-- Tabla de precios de productos --}}
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-left">

                                <thead
                                    class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-4 py-3">Escala de precio</th>
                                        <th class="px-4 py-3">Cantidad de referencia</th>
                                        <th class="px-4 py-3">Precio de venta (Q)</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 bg-white">

                                    {{-- Unitario --}}
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                            Unitario
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                            1 unidad
                                        </td>

                                        <td class="px-4 py-3">
                                            <input type="number" id="precioUnitario" name="precioUnitario"
                                                min="0" step="0.01"
                                                value="{{ old('precioUnitario', $presentacion?->precioUnitario) }}"
                                                class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm
                                                          focus:border-indigo-500 focus:ring-indigo-500">

                                            @error('precioUnitario')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>

                                    {{-- Docena --}}
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                            Docena
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                            12 unidades
                                        </td>

                                        <td class="px-4 py-3">
                                            <input type="number" id="precioUnitarioDocena"
                                                name="precioUnitarioDocena" min="0" step="0.01" required
                                                value="{{ old('precioUnitarioDocena', $presentacion?->precioUnitarioDocena) }}"
                                                class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm
                                                          focus:border-indigo-500 focus:ring-indigo-500">

                                            @error('precioUnitarioDocena')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>

                                    {{-- Ciento --}}
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                            Ciento
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                            100 unidades
                                        </td>

                                        <td class="px-4 py-3">
                                            <input type="number" id="precioUnitarioCiento"
                                                name="precioUnitarioCiento" min="0" step="0.01" required
                                                value="{{ old('precioUnitarioCiento', $presentacion?->precioUnitarioCiento) }}"
                                                class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm
                                                          focus:border-indigo-500 focus:ring-indigo-500">

                                            @error('precioUnitarioCiento')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Precio de servicio --}}
                        <div class="max-w-md">
                            <label for="precioUnitario" class="block text-sm font-medium text-gray-700">
                                Precio unitario del servicio (Q)
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="number" id="precioUnitario" name="precioUnitario" min="0"
                                step="0.01" required
                                value="{{ old('precioUnitario', $presentacion?->precioUnitario) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                          focus:border-indigo-500 focus:ring-indigo-500">

                            @error('precioUnitario')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col-reverse justify-end gap-3 pb-4 pt-6 sm:flex-row">

                <a href="{{ route('articulos.show', $articulo->id) }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white
                          px-4 py-2 text-sm font-medium text-gray-700 shadow-sm
                          transition hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent
                               bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                               shadow-sm transition hover:bg-indigo-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>
    <script>
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            const inputImagen = document.getElementById('imagen_id');
            const vistaPrevia = document.getElementById('imagen-preview');

            if (!imagen || !inputImagen || !vistaPrevia) {
                return;
            }

            // Actualizar el ID que se enviará al controlador
            inputImagen.value = imagen.id;

            // Actualizar la vista previa
            vistaPrevia.src = imagen.url;
            vistaPrevia.alt = imagen.nombre || 'Imagen del artículo';
        });
    </script>
</x-app-layout>
