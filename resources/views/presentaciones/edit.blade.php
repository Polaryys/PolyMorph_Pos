<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Editar presentación
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Modifica los datos de esta presentación y guarda los cambios.
            </p>
        </div>

        <form action="{{ route('presentaciones.update', $presentacion) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Datos de la presentación --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Detalles de la presentación
                    </h3>
                </div>

                <div class="grid grid-cols-1 items-start gap-6 p-5 md:grid-cols-[220px_minmax(0,1fr)]">
                    {{-- Imagen --}}
                    <div class="flex justify-center md:justify-start">
                        <div class="relative h-56 w-56 shrink-0">
                            <img id="imagen-preview"
                                src="{{ $presentacion->imagen
                                    ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                                    : asset('images/default-avatar.png') }}"
                                alt="Imagen de {{ $presentacion->nombre }}"
                                class="h-56 w-56 rounded border-2 border-indigo-100 bg-gray-50 object-cover shadow-sm">

                            <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                title="Seleccionar imagen" aria-label="Seleccionar imagen"
                                class="absolute bottom-2 right-2 rounded-full bg-indigo-600 p-2.5 text-white shadow-md transition hover:bg-indigo-700">
                                <x-lucide-camera class="h-4 w-4" />
                            </button>
                        </div>
                        <input type="hidden" id="imagen_id" name="imagen_id"
                            value="{{ old('imagen_id', $presentacion->imagen_id) }}">
                        @error('imagen_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="sm:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" maxlength="100" required
                                value="{{ old('nombre', $presentacion->nombre) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="codigoSKU" class="block text-sm font-medium text-gray-700">
                                Código SKU <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                                value="{{ old('codigoSKU', $presentacion->codigoSKU) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('codigoSKU')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="codigoBarras" class="block text-sm font-medium text-gray-700">
                                Código de barras
                            </label>
                            <input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                                value="{{ old('codigoBarras', $presentacion->codigoBarras) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('codigoBarras')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="factor" class="block text-sm font-medium text-gray-700">
                                Factor de conversión <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="factor" name="factor" min="1" step="1" required
                                value="{{ old('factor', $presentacion->factor) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('factor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ubicacion" class="block text-sm font-medium text-gray-700">
                                Ubicación física
                            </label>
                            <input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                                value="{{ old('ubicacion', $presentacion->ubicacion) }}"
                                placeholder="Ej. Estante A-1"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('ubicacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">
                                Fecha de vencimiento
                            </label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                                value="{{ old('fecha_vencimiento', $presentacion->fecha_vencimiento?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('fecha_vencimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select id="estado" name="estado" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="1" @selected((string) old('estado', (int) $presentacion->estado) === '1')>
                                    Activa
                                </option>
                                <option value="0" @selected((string) old('estado', (int) $presentacion->estado) === '0')>
                                    Inactiva
                                </option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 xl:col-span-4">
                            <label for="detalle" class="block text-sm font-medium text-gray-700">
                                Descripción o detalle
                            </label>
                            <textarea id="detalle" name="detalle" rows="3"
                                placeholder="Descripción adicional de la presentación..."
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('detalle', $presentacion->detalle) }}</textarea>
                            @error('detalle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Precios de venta --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Precios de venta
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        El precio unitario es obligatorio. Los precios por docena y ciento son opcionales.
                    </p>
                </div>

                <div class="p-5">
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Escala de precio</th>
                                    <th class="px-4 py-3">Cantidad de referencia</th>
                                    <th class="px-4 py-3">Precio de venta (Q)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Unitario
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        {{ $presentacion->factor }} unidades
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" id="precioUnitario" name="precioUnitario"
                                            min="0" step="0.01" required
                                            value="{{ old('precioUnitario', $presentacion->precioUnitario) }}"
                                            class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('precioUnitario')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Docena
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        12 unidades de esta presentación
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" id="precioUnitarioDocena"
                                            name="precioUnitarioDocena" min="0" step="0.01"
                                            value="{{ old('precioUnitarioDocena', $presentacion->precioUnitarioDocena) }}"
                                            class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('precioUnitarioDocena')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-800">
                                        Ciento
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                        100 unidades de esta presentación
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" id="precioUnitarioCiento"
                                            name="precioUnitarioCiento" min="0" step="0.01"
                                            value="{{ old('precioUnitarioCiento', $presentacion->precioUnitarioCiento) }}"
                                            class="block w-full min-w-32 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('precioUnitarioCiento')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 pb-4 pt-6 sm:flex-row">
                <a href="{{ route('presentaciones.show', $presentacion) }}"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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

            if (!imagen || imagen.id == null || !imagen.url || !inputImagen || !vistaPrevia) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            inputImagen.value = imagen.id;
            vistaPrevia.src = imagen.url;
            vistaPrevia.alt = imagen.nombre || 'Imagen de la presentación';
        });
    </script>
</x-app-layout>