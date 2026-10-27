<section x-cloak x-show="seleccionado" x-transition
    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
    <fieldset :disabled="!seleccionado" class="min-w-0 border-0 p-0">
        <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-[220px_minmax(0,1fr)]">
            <div>
                @include('articulos.partials._imagen')
            </div>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">
                <div class="sm:col-span-6">
                    <label for="nombre" class="block text-sm font-medium text-gray-700">
                        Nombre de la presentación <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required
                        value="{{ old('nombre') }}"
                        placeholder="Ej. Caja con 12 unidades"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nombre') border-red-500 @enderror">
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="codigoSKU" class="block text-sm font-medium text-gray-700">
                        Código SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
                        value="{{ old('codigoSKU') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('codigoSKU') border-red-500 @enderror">
                    @error('codigoSKU')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="codigoBarras" class="block text-sm font-medium text-gray-700">
                        Código de barras
                    </label>
                    <input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
                        value="{{ old('codigoBarras') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('codigoBarras') border-red-500 @enderror">
                    @error('codigoBarras')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="factor" class="block text-sm font-medium text-gray-700">
                        Factor de conversión <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="factor" name="factor" min="1" step="1" required
                        value="{{ old('factor', 1) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('factor') border-red-500 @enderror">
                    @error('factor')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="precioUnitario" class="block text-sm font-medium text-gray-700">
                        Precio unitario <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="precioUnitario" name="precioUnitario" min="0" step="0.01"
                        required value="{{ old('precioUnitario') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('precioUnitario') border-red-500 @enderror">
                    @error('precioUnitario')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700">
                        Ubicación
                    </label>
                    <input type="text" id="ubicacion" name="ubicacion" maxlength="100"
                        value="{{ old('ubicacion') }}" placeholder="Ej. Estante A-1"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('ubicacion') border-red-500 @enderror">
                    @error('ubicacion')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">
                        Fecha de vencimiento
                    </label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                        value="{{ old('fecha_vencimiento') }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('fecha_vencimiento') border-red-500 @enderror">
                    @error('fecha_vencimiento')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label for="detalle" class="block text-sm font-medium text-gray-700">
                Descripción o detalle
            </label>
            <textarea id="detalle" name="detalle" rows="3"
                placeholder="Agrega características o información adicional..."
                class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('detalle') border-red-500 @enderror">{{ old('detalle') }}</textarea>
            @error('detalle')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </fieldset>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('presentaciones.index') }}"
            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
            Volver
        </a>
        <button type="submit"
            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700">
            Guardar presentación
        </button>
    </div>
</section>
