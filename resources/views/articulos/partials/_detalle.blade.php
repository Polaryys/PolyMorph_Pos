
<div>
    <label for="detalle" class="block text-sm font-medium text-gray-700">
        Descripción o detalle
    </label>

    <textarea id="detalle" name="detalle" rows="3"
        placeholder="Agrega características o información adicional..."
        class="mt-1 block w-full rounded-md border border-gray-300
               p-2 text-sm shadow-sm focus:border-indigo-500
               focus:ring-indigo-500 @error('detalle') border-red-500 @enderror">{{ old('detalle') }}</textarea>

    @error('detalle')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
