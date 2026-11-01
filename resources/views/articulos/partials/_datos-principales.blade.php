<div x-data="{ tipo: '{{ old('tipo', '') }}' }" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">

    <!-- Tipo -->
    <div class="sm:col-span-6">
        <x-input-label for="tipo">
            Tipo <span class="text-red-500">*</span>
        </x-input-label>

        <select id="tipo" name="tipo" required x-model="tipo"
            class="mt-1 block w-40 rounded-md border border-gray-300
           p-2 text-sm shadow-sm focus:border-indigo-500
           focus:ring-indigo-500 @error('tipo') border-red-500 @enderror">

            <option value="" disabled {{ old('tipo') ? '' : 'selected' }}>
                Seleccione un tipo
            </option>

            <option value="PRODUCTO" {{ old('tipo') === 'PRODUCTO' ? 'selected' : '' }}>
                PRODUCTO
            </option>

            <option value="SERVICIO" {{ old('tipo') === 'SERVICIO' ? 'selected' : '' }}>
                SERVICIO
            </option>
        </select>

        <x-input-error :messages="$errors->get('tipo')" />
    </div>

    <div x-cloak x-show="!tipo"
        class="sm:col-span-6 rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-800">
        Selecciona un tipo de artículo para habilitar estos campos.
    </div>

    <!-- Nombre -->
    <div class="sm:col-span-6" x-bind:class="tipo ? '' : 'opacity-60'">
        <x-input-label for="nombre">
            Nombre <span class="text-red-500">*</span>
        </x-input-label>

        <x-text-input x-bind:disabled="!tipo" type="text" id="nombre" name="nombre" maxlength="100" required
            :value="old('nombre')" :error="$errors->has('nombre')" class="mt-1" />

        <x-input-error :messages="$errors->get('nombre')" />
    </div>

    <!-- Código SKU -->
    <div class="sm:col-span-2" x-bind:class="tipo ? '' : 'opacity-60'">
        <x-input-label for="codigoSKU">
            Código SKU <span class="text-red-500">*</span>
        </x-input-label>

        <x-text-input x-bind:disabled="!tipo" type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
            :value="old('codigoSKU')" :error="$errors->has('codigoSKU')" class="mt-1" />

        <x-input-error :messages="$errors->get('codigoSKU')" />
    </div>

    <!-- Categoría -->
    <div class="sm:col-span-2" x-bind:class="tipo ? '' : 'opacity-60'">
        <x-input-label for="categoria_id">
            Categoría
        </x-input-label>

        <select x-bind:disabled="!tipo" id="categoria_id" name="categoria_id"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500
                   disabled:cursor-not-allowed disabled:bg-slate-100 disabled:opacity-75
                   focus:ring-indigo-500 @error('categoria_id') border-red-500 @enderror">

            <option value="">Seleccionar categoría</option>

            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach

        </select>

        <x-input-error :messages="$errors->get('categoria_id')" />
    </div>

    <!-- Marca -->
    <div class="sm:col-span-2" x-bind:class="tipo ? '' : 'opacity-60'">
        <x-input-label for="marca_id">
            Marca
        </x-input-label>

        <select x-bind:disabled="!tipo" id="marca_id" name="marca_id"
            class="mt-1 block w-full rounded-md border border-gray-300
                    p-2 text-sm shadow-sm focus:border-indigo-500
                    disabled:cursor-not-allowed disabled:bg-slate-100 disabled:opacity-75
                    focus:ring-indigo-500 @error('marca_id') border-red-500 @enderror">

            <option value="">Seleccionar marca</option>

            @foreach ($marcas as $marca)
                <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                    {{ $marca->nombre }}
                </option>
            @endforeach

        </select>

        <x-input-error :messages="$errors->get('marca_id')" />
    </div>

</div>
