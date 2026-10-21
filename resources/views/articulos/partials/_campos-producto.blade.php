
<div id="campos-producto"
    style="display: none;"
    class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">

    <!-- Umbral -->
    <div>
        <label for="umbral" class="block text-sm font-medium text-gray-700">
            Umbral de inventario <span class="text-red-500">*</span>
        </label>

        <input type="number" id="umbral" name="umbral"
            min="0" step="1" value="{{ old('umbral', 0) }}"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500 @error('umbral') border-red-500 @enderror">

        @error('umbral')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Código de barras -->
    <div>
        <label for="codigoBarras" class="block text-sm font-medium text-gray-700">
            Código de barras
        </label>

        <input type="text" id="codigoBarras" name="codigoBarras"
            maxlength="50" value="{{ old('codigoBarras') }}"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500 @error('codigoBarras') border-red-500 @enderror">

        @error('codigoBarras')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Ubicación -->
    <div>
        <label for="ubicacion" class="block text-sm font-medium text-gray-700">
            Ubicación
        </label>

        <input type="text" id="ubicacion" name="ubicacion"
            maxlength="100" value="{{ old('ubicacion') }}"
            placeholder="Ej. Estante A-1"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500 @error('ubicacion') border-red-500 @enderror">

        @error('ubicacion')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Fecha de vencimiento -->
    <div>
        <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">
            Fecha de vencimiento
        </label>

        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
            value="{{ old('fecha_vencimiento') }}"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500 @error('fecha_vencimiento') border-red-500 @enderror">

        @error('fecha_vencimiento')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

</div>
