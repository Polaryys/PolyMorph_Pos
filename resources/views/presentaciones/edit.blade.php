<x-app-layout>
    @php
        $nombrePresentacion = trim(
            $presentacion->nombre . ($presentacion->inventario?->marca?->nombre ? ' - ' . $presentacion->inventario->marca->nombre : ''),
        );
    @endphp

    <form action="{{ route('presentaciones.update', $presentacion) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <section>
            <div class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
                <div class="grid grid-cols-1 items-start gap-6 p-5 sm:p-6 md:grid-cols-[200px_minmax(0,1fr)]">
                    <div class="flex justify-center md:justify-start">
                        <div class="relative h-48 w-48 shrink-0">
                            <img id="imagen-preview"
                                src="{{ $presentacion->imagen
                                    ? asset('storage/imagenes/' . $presentacion->imagen->nombre_archivo)
                                    : '' }}"
                                alt="{{ $nombrePresentacion }}"
                                class="{{ $presentacion->imagen ? '' : 'hidden' }} h-48 w-48 rounded-xl border border-slate-200 bg-slate-50 object-cover shadow-sm">

                            <div id="imagen-placeholder"
                                class="{{ $presentacion->imagen ? 'hidden' : 'flex' }} h-48 w-48 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 text-slate-400 shadow-sm">
                                <x-lucide-image class="h-16 w-16" />
                            </div>

                            <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                                title="Seleccionar imagen" aria-label="Seleccionar imagen"
                                class="absolute bottom-2 right-2 rounded-full bg-indigo-600 p-2.5 text-white shadow-md transition hover:bg-indigo-700">
                                <x-lucide-camera class="h-4 w-4" />
                            </button>

                            <button type="button" id="imagen-quitar" onclick="quitarImagenPresentacion()"
                                title="Quitar imagen" aria-label="Quitar imagen"
                                class="{{ $presentacion->imagen ? 'flex' : 'hidden' }} absolute left-2 top-2 items-center justify-center rounded-full bg-transparent p-1.5 text-red-600 transition hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <x-lucide-x class="h-5 w-5" />
                            </button>
                        </div>
                        <input type="hidden" id="imagen_id" name="imagen_id" value="{{ $presentacion->imagen?->id }}">
                        <x-input-error :messages="$errors->get('imagen_id')" class="mt-2" />
                    </div>

                    <div class="min-w-0 space-y-2">
                        <div class="flex flex-col justify-between gap-4 border-b border-slate-100 pb-2 lg:flex-row lg:items-start">
                            <div class="grid min-w-0 flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="nombre">
                                        Nombre <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input type="text" id="nombre" name="nombre" maxlength="100" required
                                        :value="old('nombre', $presentacion->nombre)"
                                        :error="$errors->has('nombre')" class="mt-1 rounded-lg" />
                                    <x-input-error :messages="$errors->get('nombre')" />
                                </div>

                                <x-detail-field label="Marca">
                                    {{ $presentacion->inventario?->marca?->nombre ?? '—' }}
                                </x-detail-field>
                            </div>

                            <div class="grid shrink-0 grid-cols-2 gap-3 text-sm lg:min-w-[280px]">
                                <x-detail-field label="Creado en" class="bg-slate-50">
                                    <span class="tabular-nums">{{ $presentacion->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </x-detail-field>
                                <x-detail-field label="Última edición" class="bg-slate-50">
                                    <span class="tabular-nums">{{ $presentacion->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </x-detail-field>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-x-3 gap-y-2 sm:grid-cols-2 xl:grid-cols-3">
                            <div>
                                <x-input-label for="estado">
                                    Estado <span class="text-red-500">*</span>
                                </x-input-label>
                                <select id="estado" name="estado" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" @selected((string) old('estado', (int) $presentacion->estado) === '1')>Activa</option>
                                    <option value="0" @selected((string) old('estado', (int) $presentacion->estado) === '0')>Inactiva</option>
                                </select>
                                <x-input-error :messages="$errors->get('estado')" />
                            </div>

                            <div>
                                <x-input-label for="codigoSKU">
                                    SKU <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                                    :value="old('codigoSKU', $presentacion->codigoSKU)"
                                    :error="$errors->has('codigoSKU')" class="mt-1 rounded-lg" />
                                <x-input-error :messages="$errors->get('codigoSKU')" />
                            </div>

                            <div>
                                <x-input-label for="codigoBarras">Código de barras</x-input-label>
                                <x-text-input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                                    :value="old('codigoBarras', $presentacion->codigoBarras)"
                                    :error="$errors->has('codigoBarras')" class="mt-1 rounded-lg" />
                                <x-input-error :messages="$errors->get('codigoBarras')" />
                            </div>

                            <div>
                                <x-input-label for="factor">
                                    Factor de conversión <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input type="number" id="factor" name="factor" min="1" step="1" required
                                    :value="old('factor', $presentacion->factor)"
                                    :error="$errors->has('factor')" class="mt-1 rounded-lg" />
                                <x-input-error :messages="$errors->get('factor')" />
                            </div>

                            <div>
                                <x-input-label for="fecha_vencimiento">Vencimiento</x-input-label>
                                <x-text-input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                                    :value="old('fecha_vencimiento', $presentacion->fecha_vencimiento?->format('Y-m-d'))"
                                    :error="$errors->has('fecha_vencimiento')" class="mt-1 rounded-lg" />
                                <x-input-error :messages="$errors->get('fecha_vencimiento')" />
                            </div>

                            <div>
                                <x-input-label for="ubicacion">Ubicación física</x-input-label>
                                <x-text-input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                                    :value="old('ubicacion', $presentacion->ubicacion)"
                                    placeholder="Ej. Estante A-1" :error="$errors->has('ubicacion')" class="mt-1 rounded-lg" />
                                <x-input-error :messages="$errors->get('ubicacion')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="detalle">Detalles</x-input-label>
                            <textarea id="detalle" name="detalle" rows="2"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Descripción adicional de la presentación...">{{ old('detalle', $presentacion->detalle) }}</textarea>
                            <x-input-error :messages="$errors->get('detalle')" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <x-section-title>Precios de venta</x-section-title>
            <p class="mt-1 text-sm text-slate-500">
                El precio unitario es obligatorio. Los precios por docena y ciento son opcionales.
            </p>
            <x-table>
                <x-slot:headers>
                    <th class="px-4 py-3">Escala de precio</th>
                    <th class="px-4 py-3">Cantidad de referencia</th>
                    <th class="px-4 py-3">Precio de venta (Q)</th>
                </x-slot:headers>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Unitario</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $presentacion->factor }} unidades</td>
                    <td class="px-4 py-3">
                        <x-text-input type="number" id="precioUnitario" name="precioUnitario" min="0" step="0.01"
                            required :value="old('precioUnitario', $presentacion->precioUnitario)"
                            :error="$errors->has('precioUnitario')" class="min-w-32 rounded-lg" />
                        <x-input-error :messages="$errors->get('precioUnitario')" />
                    </td>
                </tr>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Docena</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">12 unidades de esta presentación</td>
                    <td class="px-4 py-3">
                        <x-text-input type="number" id="precioUnitarioDocena" name="precioUnitarioDocena" min="0"
                            step="0.01" :value="old('precioUnitarioDocena', $presentacion->precioUnitarioDocena)"
                            :error="$errors->has('precioUnitarioDocena')" class="min-w-32 rounded-lg" />
                        <x-input-error :messages="$errors->get('precioUnitarioDocena')" />
                    </td>
                </tr>
                <tr>
                    <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800">Ciento</td>
                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">100 unidades de esta presentación</td>
                    <td class="px-4 py-3">
                        <x-text-input type="number" id="precioUnitarioCiento" name="precioUnitarioCiento" min="0"
                            step="0.01" :value="old('precioUnitarioCiento', $presentacion->precioUnitarioCiento)"
                            :error="$errors->has('precioUnitarioCiento')" class="min-w-32 rounded-lg" />
                        <x-input-error :messages="$errors->get('precioUnitarioCiento')" />
                    </td>
                </tr>
            </x-table>
        </section>

        <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 pt-5 sm:flex-row">
            <x-secondary-link href="{{ route('presentaciones.show', $presentacion) }}">
                Cancelar
            </x-secondary-link>
            <x-primary-button>
                Guardar cambios
            </x-primary-button>
        </div>
    </form>

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
            vistaPrevia.classList.remove('hidden');
            document.getElementById('imagen-placeholder').classList.add('hidden');
            document.getElementById('imagen-quitar').classList.replace('hidden', 'flex');
        });

        function quitarImagenPresentacion() {
            document.getElementById('imagen_id').value = '';
            document.getElementById('imagen-preview').src = '';
            document.getElementById('imagen-preview').classList.add('hidden');
            document.getElementById('imagen-placeholder').classList.replace('hidden', 'flex');
            document.getElementById('imagen-quitar').classList.replace('flex', 'hidden');
        }
    </script>
</x-app-layout>
