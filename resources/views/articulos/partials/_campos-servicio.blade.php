
<div id="campos-servicio"
    style="display: none;"
    class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">

    <div class="sm:col-span-1">
        <label for="precioUnitario" class="block text-sm font-medium text-gray-700">
            Precio unitario (Q) <span class="text-red-500">*</span>
        </label>

        <input type="number" id="precioUnitario" name="precioUnitario"
            min="0" step="0.01"
            value="{{ old('precioUnitario') }}"
            placeholder="0.00"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   focus:ring-indigo-500 @error('precioUnitario') border-red-500 @enderror">

        @error('precioUnitario')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

</div>
