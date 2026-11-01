
<div id="campos-producto"
    style="display: none;"
    class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">

    <!-- Umbral -->
    <div>
        <x-input-label for="umbral">
            Umbral de inventario <span class="text-red-500">*</span>
        </x-input-label>

        <x-text-input type="number" id="umbral" name="umbral" min="0" step="1"
            :value="old('umbral', 0)" :error="$errors->has('umbral')" class="mt-1" />

        <x-input-error :messages="$errors->get('umbral')" />
    </div>

    <!-- Código de barras -->
    <div>
        <x-input-label for="codigoBarras">
            Código de barras
        </x-input-label>

        <x-text-input type="text" id="codigoBarras" name="codigoBarras" maxlength="50"
            :value="old('codigoBarras')" :error="$errors->has('codigoBarras')" class="mt-1" />

        <x-input-error :messages="$errors->get('codigoBarras')" />
    </div>

    <!-- Ubicación -->
    <div>
        <x-input-label for="ubicacion">
            Ubicación
        </x-input-label>

        <x-text-input type="text" id="ubicacion" name="ubicacion" maxlength="100"
            :value="old('ubicacion')" placeholder="Ej. Estante A-1"
            :error="$errors->has('ubicacion')" class="mt-1" />

        <x-input-error :messages="$errors->get('ubicacion')" />
    </div>

    <!-- Fecha de vencimiento -->
    <div>
        <x-input-label for="fecha_vencimiento">
            Fecha de vencimiento
        </x-input-label>

        <x-text-input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
            :value="old('fecha_vencimiento')" :error="$errors->has('fecha_vencimiento')" class="mt-1" />

        <x-input-error :messages="$errors->get('fecha_vencimiento')" />
    </div>

</div>
