<x-app-layout>
    @php
        $presentacion = $articulo->presentaciones->firstWhere('base', true) ?? $articulo->presentaciones->first();
    @endphp

    <div class="space-y-6">

        <form action="{{ route('articulos.update', $articulo->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Información general --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50/75 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-600">
                        Detalles del {{ $articulo->tipo === 'PRODUCTO' ? 'producto' : 'servicio' }}
                    </h3>
                </div>

                <div class="grid grid-cols-1 items-start gap-6 p-5 md:grid-cols-[220px_minmax(0,1fr)]">

                    {{-- Imagen --}}
                    <div class="flex justify-center md:justify-start">
                        <div class="relative h-56 w-56 shrink-0">
                            <img id="imagen-preview"
                                src="{{ $presentacion?->imagen
                                    ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                                    : asset('images/default-avatar.png') }}"
                                alt="Imagen del artículo"
                                class="h-56 w-56 rounded border-2 border-indigo-100 bg-gray-50 object-cover shadow-sm">

                            <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                title="Seleccionar imagen" aria-label="Seleccionar imagen"
                                class="absolute bottom-2 right-2 rounded-full bg-indigo-600 p-2.5 text-white shadow-md transition hover:bg-indigo-700">
                                <x-lucide-camera class="h-4 w-4" />
                            </button>
                        </div>
                        <input type="hidden" id="imagen_id" name="imagen_id"
                            value="{{ old('imagen_id', $presentacion?->imagen_id) }}">
                        <x-input-error :messages="$errors->get('imagen_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 gap-x-4 gap-y-5 sm:grid-cols-2 xl:grid-cols-4">

                    {{-- Tipo: no editable --}}
                    <div>
                        <x-input-label>
                            Tipo de artículo
                        </x-input-label>

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
                        <x-input-label for="categoria_id">
                            Categoría
                        </x-input-label>

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

                        <x-input-error :messages="$errors->get('categoria_id')" />
                    </div>

                    {{-- Marca --}}
                    <div>
                        <x-input-label for="marca_id">
                            Marca
                        </x-input-label>

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

                        <x-input-error :messages="$errors->get('marca_id')" />
                    </div>

                    {{-- Estado --}}
                    <div>
                        <x-input-label for="estado">
                            Estado del artículo <span class="text-red-500">*</span>
                        </x-input-label>

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

                        <x-input-error :messages="$errors->get('estado')" />
                    </div>

                    {{-- Umbral --}}
                    <div>
                        <x-input-label for="umbral">
                            Umbral de inventario <span class="text-red-500">*</span>
                        </x-input-label>

                        <x-text-input type="number" id="umbral" name="umbral" min="0" step="1" required
                            :value="old('umbral', $articulo->umbral ?? 0)"
                            :error="$errors->has('umbral')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('umbral')" />
                    </div>

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
                        <x-input-label for="nombre">
                            Nombre <span class="text-red-500">*</span>
                        </x-input-label>

                        <x-text-input type="text" id="nombre" name="nombre" maxlength="100" required
                            :value="old('nombre', $presentacion?->nombre)"
                            :error="$errors->has('nombre')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>

                    {{-- SKU --}}
                    <div>
                        <x-input-label for="codigoSKU">
                            Código SKU <span class="text-red-500">*</span>
                        </x-input-label>

                        <x-text-input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                            :value="old('codigoSKU', $presentacion?->codigoSKU)"
                            :error="$errors->has('codigoSKU')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('codigoSKU')" />
                    </div>

                    {{-- Código de barras --}}
                    <div>
                        <x-input-label for="codigoBarras">
                            Código de barras
                        </x-input-label>

                        <x-text-input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                            :value="old('codigoBarras', $presentacion?->codigoBarras)"
                            :error="$errors->has('codigoBarras')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('codigoBarras')" />
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <x-input-label for="ubicacion">
                            Ubicación física
                        </x-input-label>

                        <x-text-input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                            :value="old('ubicacion', $presentacion?->ubicacion)" placeholder="Ej. Estante A-1"
                            :error="$errors->has('ubicacion')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('ubicacion')" />
                    </div>

                    {{-- Fecha de vencimiento --}}
                    <div>
                        <x-input-label for="fecha_vencimiento">
                            Fecha de vencimiento
                        </x-input-label>

                        <x-text-input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                            :value="old(
                                'fecha_vencimiento',
                                $presentacion?->fecha_vencimiento ? \Carbon\Carbon::parse($presentacion->fecha_vencimiento)->format('Y-m-d') : '',
                            )" :error="$errors->has('fecha_vencimiento')" class="mt-1 rounded-lg" />

                        <x-input-error :messages="$errors->get('fecha_vencimiento')" />
                    </div>

                    {{-- Detalle --}}
                    <div class="sm:col-span-2 xl:col-span-4">
                        <x-input-label for="detalle">
                            Descripción o detalle
                        </x-input-label>

                        <textarea id="detalle" name="detalle" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm
                                         focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Descripción adicional del artículo...">{{ old('detalle', $presentacion?->detalle) }}</textarea>

                        <x-input-error :messages="$errors->get('detalle')" />
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
                                            <x-text-input type="number" id="precioUnitario" name="precioUnitario"
                                                min="0" step="0.01"
                                                :value="old('precioUnitario', $presentacion?->precioUnitario)"
                                                :error="$errors->has('precioUnitario')"
                                                class="min-w-32 rounded-lg" />

                                            <x-input-error :messages="$errors->get('precioUnitario')" />
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
                                            <x-text-input type="number" id="precioUnitarioDocena"
                                                name="precioUnitarioDocena" min="0" step="0.01" required
                                                :value="old('precioUnitarioDocena', $presentacion?->precioUnitarioDocena)"
                                                :error="$errors->has('precioUnitarioDocena')"
                                                class="min-w-32 rounded-lg" />

                                            <x-input-error :messages="$errors->get('precioUnitarioDocena')" />
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
                                            <x-text-input type="number" id="precioUnitarioCiento"
                                                name="precioUnitarioCiento" min="0" step="0.01" required
                                                :value="old('precioUnitarioCiento', $presentacion?->precioUnitarioCiento)"
                                                :error="$errors->has('precioUnitarioCiento')"
                                                class="min-w-32 rounded-lg" />

                                            <x-input-error :messages="$errors->get('precioUnitarioCiento')" />
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Precio de servicio --}}
                        <div class="max-w-md">
                            <x-input-label for="precioUnitario">
                                Precio unitario del servicio (Q)
                                <span class="text-red-500">*</span>
                            </x-input-label>

                            <x-text-input type="number" id="precioUnitario" name="precioUnitario" min="0"
                                step="0.01" required :value="old('precioUnitario', $presentacion?->precioUnitario)"
                                :error="$errors->has('precioUnitario')" class="mt-1 rounded-lg" />

                            <x-input-error :messages="$errors->get('precioUnitario')" />
                        </div>
                    @endif

                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col-reverse justify-end gap-3 pb-4 pt-6 pb-6 sm:flex-row">

                <x-secondary-link href="{{ route('articulos.index') }}">
                    Cancelar
                </x-secondary-link>

                <x-primary-button>
                    Guardar Cambios
                </x-primary-button>

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
