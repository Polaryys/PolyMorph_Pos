
<div id="campos-servicio"
    style="display: none;"
    class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">

    <div class="sm:col-span-1">
        <x-input-label for="precioUnitario">
            Precio unitario (Q) <span class="text-red-500">*</span>
        </x-input-label>

        <x-text-input type="number" id="precioUnitario" name="precioUnitario" min="0" step="0.01"
            :value="old('precioUnitario')" placeholder="0.00"
            :error="$errors->has('precioUnitario')" class="mt-1" />

        <x-input-error :messages="$errors->get('precioUnitario')" />
    </div>

</div>
