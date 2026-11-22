<x-app-layout>

    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-8">

            <!-- datos del cliente -->
            <div class="space-y-4">
                <x-section-title>
                    Datos del Cliente
                </x-section-title>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">
                    <!-- NIT -->
                    <div class="sm:col-span-3">
                        <x-input-label for="nit">
                            NIT <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input type="text" id="nit" name="nit" maxlength="15" :value="old('nit', $cliente->nit)"
                            :error="$errors->has('nit')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nit')" />
                    </div>

                    <!-- CLASIFICACIÓN -->
                    <div class="sm:col-span-3">
                        <x-input-label for="clasificacion">
                            Clasificación <span class="text-red-500">*</span>
                        </x-input-label>

                        <select id="clasificacion" name="clasificacion"
                            class="mt-1 block w-full rounded-md border-app-border-strong shadow-sm
                       focus:border-app-primary-500 focus:ring-app-primary-500 sm:text-sm p-2 border
                       @error('clasificacion') border-red-500 @enderror">

                            <option value="Mayorista" class="bg-app-background"
                                {{ old('clasificacion', $cliente->clasificacion) == 'Mayorista' ? 'selected' : '' }}>
                                Mayorista
                            </option>

                            <option value="Minorista" class="bg-app-background"
                                {{ old('clasificacion', $cliente->clasificacion) == 'Minorista' ? 'selected' : '' }}>
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
                        <x-text-input type="text" id="nombres" name="nombres" maxlength="100" :value="old('nombres', $cliente->nombres)"
                            :error="$errors->has('nombres')" class="mt-1" />
                        <x-input-error :messages="$errors->get('nombres')" />
                    </div>

                    <!-- APELLIDOS -->
                    <div class="sm:col-span-3">
                        <x-input-label for="apellidos">
                            Apellidos
                        </x-input-label>
                        <x-text-input type="text" id="apellidos" name="apellidos" maxlength="100" :value="old('apellidos', $cliente->apellidos)"
                            :error="$errors->has('apellidos')" class="mt-1" />
                        <x-input-error :messages="$errors->get('apellidos')" />
                    </div>

                </div>
            </div>


            <!-- info contacto -->
            <div class="pt-4 space-y-4">
                <x-section-title>
                    Información de Contaacto
                </x-section-title>

                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">

                    <!-- TELÉFONO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="telefono">
                            Teléfono
                        </x-input-label>
                        <x-text-input type="text" id="telefono" name="telefono" maxlength="15" :value="old('telefono', $cliente->telefono)"
                            :error="$errors->has('telefono')" class="mt-1" />
                        <x-input-error :messages="$errors->get('telefono')" />
                    </div>

                    <!-- CORREO -->
                    <div class="sm:col-span-3">
                        <x-input-label for="correo">
                            Correo electrónico
                        </x-input-label>
                        <x-text-input type="email" id="correo" name="correo" maxlength="100" :value="old('correo', $cliente->correo)"
                            :error="$errors->has('correo')" class="mt-1" />
                        <x-input-error :messages="$errors->get('correo')" />
                    </div>

                    <!-- NOTAS -->
                    <div class="sm:col-span-6">
                        <x-input-label for="notas">
                            Notas
                        </x-input-label>
                        <textarea id="notas" name="notas" maxlength="255" rows="3"
                            class="mt-1 block w-full rounded-md border border-app-border-strong p-2
                text-sm shadow-sm focus:ring-app-primary-500 focus:border-app-primary-400
                @error('notas') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('notas', $cliente->notas) }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" />
                    </div>

                </div>
            </div>

        </div>
        <div class="mb-6 mt-3 flex justify-end space-x-3">

            <x-secondary-link href="{{ route('clientes.index') }}">
                Volver
            </x-secondary-link>

            <x-primary-button>
                Actualizar clientes
            </x-primary-button>

        </div>

    </form>

</x-app-layout>
