<x-app-layout>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <!-- BOTONES -->
        <div class="mb-3 mt-1 flex justify-start space-x-3">

            <a href="{{ route('clientes.index') }}"
                class="rounded-md border border-gray-300 bg-white py-2 px-4
                       text-sm font-medium text-gray-700 shadow-sm
                       hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                       shadow-sm hover:bg-indigo-700 transition">
                Guardar Cliente
            </button>

        </div>

        <div class="space-y-8 divide-y divide-gray-200">

            <!-- datos del cliente -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                    Datos del Cliente
                </h3>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NIT -->
                    <div class="sm:col-span-3">
                        <label for="nit" class="block text-sm font-medium text-gray-700">
                            NIT <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nit" name="nit" maxlength="15" value="{{ old('nit') }} CF"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('nit') border-red-500 @enderror">

                        @error('nit')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CLASIFICACIÓN -->
                    <div class="sm:col-span-3">
                        <label for="clasificacion" class="block text-sm font-medium text-gray-700">
                            Clasificación <span class="text-red-500">*</span>
                        </label>

                        <select id="clasificacion" name="clasificacion"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
           @error('clasificacion') border-red-500 @enderror">
                            <option value="Mayorista"
                                {{ old('clasificacion', 'Minorista') == 'Mayorista' ? 'selected' : '' }}>
                                Mayorista
                            </option>

                            <option value="Minorista"
                                {{ old('clasificacion', 'Minorista') == 'Minorista' ? 'selected' : '' }}>
                                Minorista
                            </option>
                        </select>

                        @error('clasificacion')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NOMBRES -->
                    <div class="sm:col-span-3">
                        <label for="nombres" class="block text-sm font-medium text-gray-700">
                            Nombres <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nombres" name="nombres" maxlength="100" value="{{ old('nombres') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('nombres') border-red-500 @enderror">

                        @error('nombres')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- APELLIDOS -->
                    <div class="sm:col-span-3">
                        <label for="apellidos" class="block text-sm font-medium text-gray-700">
                            Apellidos
                        </label>

                        <input type="text" id="apellidos" name="apellidos" maxlength="100"
                            value="{{ old('apellidos') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('apellidos') border-red-500 @enderror">

                        @error('apellidos')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>


            <!-- info contacto -->
            <div class="pt-4 space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                    Información de Contacto
                </h3>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- TELÉFONO -->
                    <div class="sm:col-span-3">
                        <label for="telefono" class="block text-sm font-medium text-gray-700">
                            Teléfono
                        </label>

                        <input type="text" id="telefono" name="telefono" maxlength="15"
                            value="{{ old('telefono') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('telefono') border-red-500 @enderror">

                        @error('telefono')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CORREO -->
                    <div class="sm:col-span-3">
                        <label for="correo" class="block text-sm font-medium text-gray-700">
                            Correo electrónico
                        </label>

                        <input type="email" id="correo" name="correo" maxlength="100" value="{{ old('correo') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('correo') border-red-500 @enderror">

                        @error('correo')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NOTAS -->
                    <div class="sm:col-span-6">
                        <label for="notas" class="block text-sm font-medium text-gray-700">
                            Notas
                        </label>

                        <textarea id="notas" name="notas" maxlength="255" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                           @error('notas') border-red-500 @enderror">{{ old('notas') }}</textarea>

                        @error('notas')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

    </form>

</x-app-layout>
