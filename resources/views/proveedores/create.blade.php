<x-app-layout>

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="space-y-8 divide-y divide-gray-200">

            <!-- datos de proveedor -->
            <div class="space-y-4">
                <x-section-title>
                    Datos del Proveedor
                </x-section-title>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NIT -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nit">
                            NIT <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="nit" name="nit" maxlength="15" placeholder="NIT o CF"
                            :value="old('nit')" :error="$errors->has('nit')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nit')" />
                    </div>

                    <!-- NOMBRE -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nombre">
                            Nombre <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="nombre" name="nombre" maxlength="150" :value="old('nombre')"
                            :error="$errors->has('nombre')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nombre')" />
                    </div>

                    <!-- TELÉFONO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="telefono">
                            Teléfono <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="telefono" name="telefono" maxlength="20" :value="old('telefono')"
                            :error="$errors->has('telefono')" class="mt-1" />
                        <x-input-error :messages="$errors->get('telefono')" />
                    </div>

                    <!-- CORREO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="correo">
                            Correo electrónico
                        </x-input-label>
                        <x-text-input type="email" id="correo" name="correo" maxlength="255" :value="old('correo')"
                            :error="$errors->has('correo')" class="mt-1" />
                        <x-input-error :messages="$errors->get('correo')" />
                    </div>

                </div>
            </div>

            <!-- detalles de contacto -->
            <div class="pt-4 space-y-4">
                <x-section-title>
                    Información de Contacto
                </x-section-title>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NOMBRE DEL CONTACTO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nombre_contacto">
                            Nombre del contacto
                        </x-input-label>
                        <x-text-input type="text" id="nombre_contacto" name="nombre_contacto" maxlength="150"
                            :value="old('nombre_contacto')" :error="$errors->has('nombre_contacto')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nombre_contacto')" />
                    </div>

                    <!-- TELÉFONO DEL CONTACTO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="telefono_contacto">
                            Teléfono del contacto <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="telefono_contacto" name="telefono_contacto" maxlength="20"
                            :value="old('telefono_contacto')" :error="$errors->has('telefono_contacto')" class="mt-1" />
                        <x-input-error :messages="$errors->get('telefono_contacto')" />
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="sm:col-span-3">
                        <x-input-label for="direccion">
                            Dirección
                        </x-input-label>
                        <x-text-input type="text" id="direccion" name="direccion" maxlength="255" :value="old('direccion')"
                            :error="$errors->has('direccion')" class="mt-1" />
                        <x-input-error :messages="$errors->get('direccion')" />
                    </div>

                    <!-- NOTAS -->
                    <div class="sm:col-span-3">
                        <x-input-label for="notas">
                            Notas
                        </x-input-label>

                        <textarea id="notas" name="notas" maxlength="255" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                   focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border
                                   @error('notas') border-red-500 @enderror">{{ old('notas') }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" />
                    </div>



                </div>
            </div>
            <div class="mt-4 flex justify-end gap-3 border-t border-slate-200 pt-4 pb-6">
            <x-secondary-link href="{{ route('proveedores.index') }}">
                Volver
            </x-secondary-link>

            <x-primary-button>
                Guardar Proveedor
            </x-primary-button>
        </div>

        </div>
        

    </form>

</x-app-layout>
