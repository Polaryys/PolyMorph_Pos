<section x-cloak x-transition
    class="pt-4 shadow-sm sm:p-4">
    <fieldset :disabled="!productoSeleccionado" class="min-w-0 border-0 p-0">
        <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-[220px_minmax(0,1fr)]">
            <div>
                @php
                    $imagenSeleccionada = null;
                    $mostrarBotonQuitarImagen = false;
                @endphp
                @include('articulos.partials._imagen')
            </div>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <x-input-label for="nombre">
                        Nombre de la presentación <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-text-input type="text" id="nombre" name="nombre" maxlength="100" required
                        :value="old('nombre')" placeholder="Ej. Caja con 12 unidades"
                        :error="$errors->has('nombre')" class="mt-1" />
                    <x-input-error :messages="$errors->get('nombre')" />
                </div>

                <div class="sm:col-span-3">
                    <x-input-label for="codigoSKU">
                        Código SKU <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-text-input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                        :value="old('codigoSKU')" :error="$errors->has('codigoSKU')" class="mt-1" />
                    <x-input-error :messages="$errors->get('codigoSKU')" />
                </div>

                <div class="sm:col-span-3">
                    <x-input-label for="codigoBarras">
                        Código de barras
                    </x-input-label>
                    <x-text-input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                        :value="old('codigoBarras')" :error="$errors->has('codigoBarras')" class="mt-1" />
                    <x-input-error :messages="$errors->get('codigoBarras')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="factor">
                        Factor de conversión <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-text-input type="number" id="factor" name="factor" min="1" step="1" required
                        x-model.number="factorPresentacion" :error="$errors->has('factor')" class="mt-1" />
                    <x-input-error :messages="$errors->get('factor')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="costoPromedioPresentacion">
                        Costo promedio de la presentación
                    </x-input-label>
                    <input type="text" id="costoPromedioPresentacion" readonly tabindex="-1" aria-readonly="true"
                        :value="(
                            Number(productoSeleccionadoData?.costoPromedioUnitario ?? 0)
                            * Number(factorPresentacion || 0)
                        ).toFixed(2)"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm">
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="precioUnitario">
                        Precio unitario <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-text-input type="number" id="precioUnitario" name="precioUnitario" min="0" step="0.01"
                        required :value="old('precioUnitario')" :error="$errors->has('precioUnitario')" class="mt-1" />
                    <x-input-error :messages="$errors->get('precioUnitario')" />
                </div>

                <div class="sm:col-span-3">
                    <x-input-label for="ubicacion">
                        Ubicación
                    </x-input-label>
                    <x-text-input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                        :value="old('ubicacion')" placeholder="Ej. Estante A-1"
                        :error="$errors->has('ubicacion')" class="mt-1" />
                    <x-input-error :messages="$errors->get('ubicacion')" />
                </div>

                <div class="sm:col-span-3">
                    <x-input-label for="fecha_vencimiento">
                        Fecha de vencimiento
                    </x-input-label>
                    <x-text-input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                        :value="old('fecha_vencimiento')" :error="$errors->has('fecha_vencimiento')" class="mt-1" />
                    <x-input-error :messages="$errors->get('fecha_vencimiento')" />
                </div>
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="detalle">
                Descripción o detalle
            </x-input-label>
            <textarea id="detalle" name="detalle" rows="3"
                placeholder="Agrega características o información adicional..."
                class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('detalle') border-red-500 @enderror">{{ old('detalle') }}</textarea>
            <x-input-error :messages="$errors->get('detalle')" />
        </div>
    </fieldset>

    <div class="mt-6 flex justify-end gap-3">
        <x-secondary-link href="{{ route('presentaciones.index') }}">
            Volver
        </x-secondary-link>
        <x-primary-button>
            Guardar presentación
        </x-primary-button>
    </div>
</section>
