<div x-data="{ tipo: '{{ old('tipo', '') }}' }" class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-6">

    <!-- Tipo -->
    <div class="sm:col-span-6">
        <label for="tipo" class="block text-sm font-medium text-gray-700">
            Tipo <span class="text-red-500">*</span>
        </label>

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

        @error('tipo')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nombre -->
    <div class="sm:col-span-6">
        <label for="nombre" class="block text-sm font-medium text-gray-700">
            Nombre <span class="text-red-500">*</span>
        </label>

        <input :disabled="!tipo" type="text" id="nombre" name="nombre" maxlength="100" required
            value="{{ old('nombre') }}"
            class="mt-1 block w-full rounded-md border border-gray-300
           p-2 text-sm shadow-sm focus:border-indigo-500
           focus:ring-indigo-500 disabled:bg-gray-100
           disabled:cursor-not-allowed
           @error('nombre') border-red-500 @enderror">

        @error('nombre')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Código SKU -->
    <div class="sm:col-span-2">
        <label for="codigoSKU" class="block text-sm font-medium text-gray-700">
            Código SKU <span class="text-red-500">*</span>
        </label>

        <input :disabled="!tipo" type="text" id="codigoSKU" name="codigoSKU" maxlength="50" required
            value="{{ old('codigoSKU') }}"
            class="mt-1 block w-full rounded-md border border-gray-300
                    p-2 text-sm shadow-sm focus:border-indigo-500
                    focus:ring-indigo-500 disabled:bg-gray-100
                    disabled:cursor-not-allowed
                    @error('codigoSKU') border-red-500 @enderror">

        @error('codigoSKU')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Categoría -->
    <div class="sm:col-span-2">
        <label for="categoria_id" class="block text-sm font-medium text-gray-700">
            Categoría
        </label>

        <select :disabled="!tipo" disabled:bg-gray-100 disabled:cursor-not-allowed id="categoria_id"
            name="categoria_id"
            class="mt-1 block w-full rounded-md border border-gray-300
                   p-2 text-sm shadow-sm focus:border-indigo-500 
                   disabled:bg-gray-100 disabled:cursor-not-allowed
                   focus:ring-indigo-500 @error('categoria_id') border-red-500 @enderror">

            <option value="">Seleccionar categoría</option>

            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach

        </select>

        @error('categoria_id')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Marca -->
    <div class="sm:col-span-2">
        <label for="marca_id" class="block text-sm font-medium text-gray-700">
            Marca
        </label>

        <select :disabled="!tipo" disabled:bg-gray-50 disabled:cursor-not-allowed id="marca_id" name="marca_id"
            class="mt-1 block w-full rounded-md border border-gray-300
                    p-2 text-sm shadow-sm focus:border-indigo-500
                    disabled:bg-gray-100
                    disabled:cursor-not-allowed
                    focus:ring-indigo-500 @error('marca_id') border-red-500 @enderror">

            <option value="">Seleccionar marca</option>

            @foreach ($marcas as $marca)
                <option value="{{ $marca->id }}" {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                    {{ $marca->nombre }}
                </option>
            @endforeach

        </select>

        @error('marca_id')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

</div>
