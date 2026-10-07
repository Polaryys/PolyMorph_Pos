<x-app-layout>

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="mb-3 mt-1 flex justify-start space-x-3">

            <a href="{{ route('proveedores.index') }}"
                class="rounded-md border border-gray-300 bg-white py-2 px-4
                       text-sm font-medium text-gray-700 shadow-sm
                       hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="inline-flex justify-center rounded-md border border-transparent
                       bg-indigo-600 py-2 px-4 text-sm font-medium text-white
                       shadow-sm hover:bg-indigo-700 transition">
                Guardar Proveedor
            </button>

        </div>

        <div class="space-y-8 divide-y divide-gray-200">

            <!-- datos de proveedor -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                    Datos del Proveedor
                </h3>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NIT -->
                    <div class="sm:col-span-3">
                        <label for="nit" class="block text-sm font-medium text-gray-700">
                            NIT <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nit" name="nit" maxlength="15"
                            value="{{ old('nit') }}"
                            placeholder="NIT o CF"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('nit') border-red-500 @enderror">

                        @error('nit')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NOMBRE -->
                    <div class="sm:col-span-3">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nombre" name="nombre" maxlength="150"
                            value="{{ old('nombre') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('nombre') border-red-500 @enderror">

                        @error('nombre')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TELÉFONO -->
                    <div class="sm:col-span-3">
                        <label for="telefono" class="block text-sm font-medium text-gray-700">
                            Teléfono <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="telefono" name="telefono" maxlength="20"
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

                        <input type="email" id="correo" name="correo" maxlength="255"
                            value="{{ old('correo') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('correo') border-red-500 @enderror">

                        @error('correo')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- detalles de contacto -->
            <div class="pt-4 space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
                    Información de Contacto
                </h3>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NOMBRE DEL CONTACTO -->
                    <div class="sm:col-span-3">
                        <label for="nombre_contacto" class="block text-sm font-medium text-gray-700">
                            Nombre del contacto
                        </label>

                        <input type="text" id="nombre_contacto" name="nombre_contacto" maxlength="150"
                            value="{{ old('nombre_contacto') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('nombre_contacto') border-red-500 @enderror">

                        @error('nombre_contacto')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TELÉFONO DEL CONTACTO -->
                    <div class="sm:col-span-3">
                        <label for="telefono_contacto" class="block text-sm font-medium text-gray-700">
                            Teléfono del contacto <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="telefono_contacto" name="telefono_contacto" maxlength="20"
                            value="{{ old('telefono_contacto') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('telefono_contacto') border-red-500 @enderror">

                        @error('telefono_contacto')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="sm:col-span-6">
                        <label for="direccion" class="block text-sm font-medium text-gray-700">
                            Dirección
                        </label>

                        <input type="text" id="direccion" name="direccion" maxlength="255"
                            value="{{ old('direccion') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('direccion') border-red-500 @enderror">

                        @error('direccion')
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