<x-app-layout>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="space-y-8 divide-y divide-gray-200">

            <!-- datos del cliente -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold leading-6 text-indigo-900 flex items-center gap-2">
                    Datos del Cliente
                </h3>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- NIT -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nit">
                            NIT <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="nit" name="nit" maxlength="15" :value="old('nit', 'CF')"
                            :error="$errors->has('nit')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nit')" />
                    </div>

                    <!-- CLASIFICACIÓN -->
                    <div class="sm:col-span-3">
                        <x-input-label for="clasificacion">
                            Clasificación <span class="text-red-500">*</span>
                        </x-input-label>

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

                        <x-input-error :messages="$errors->get('clasificacion')" />
                    </div>

                    <!-- NOMBRES -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nombres">
                            Nombres <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="nombres" name="nombres" maxlength="100" :value="old('nombres')"
                            :error="$errors->has('nombres')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nombres')" />
                    </div>

                    <!-- APELLIDOS -->
                    <div class="sm:col-span-3">
                        <x-input-label for="apellidos">
                            Apellidos
                        </x-input-label>
                        <x-text-input type="text" id="apellidos" name="apellidos" maxlength="100" :value="old('apellidos')"
                            :error="$errors->has('apellidos')" class="mt-1" />
                        <x-input-error :messages="$errors->get('apellidos')" />
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
                        <x-input-label for="telefono">
                            Teléfono
                        </x-input-label>
                        <x-text-input type="text" id="telefono" name="telefono" maxlength="15" :value="old('telefono')"
                            :error="$errors->has('telefono')" class="mt-1" />
                        <x-input-error :messages="$errors->get('telefono')" />
                    </div>

                    <!-- CORREO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="correo">
                            Correo Electrónico
                        </x-input-label>

                        <x-text-input type="email" id="correo" name="correo" maxlength="100" :value="old('correo')"
                            :error="$errors->has('correo')" class="mt-1" />

                        <x-input-error :messages="$errors->get('correo')" />
                    </div>

                    <!-- NOTAS -->
                    <div class="sm:col-span-6">
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

        </div>

        <!-- Botones de Acción -->
        <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-5 pb-6">
            <x-secondary-link href="{{ route('clientes.index') }}">
                Volver
            </x-secondary-link>

            <x-primary-button>
                Guardar Cliente
            </x-primary-button>
        </div>

    </form>

</x-app-layout>
