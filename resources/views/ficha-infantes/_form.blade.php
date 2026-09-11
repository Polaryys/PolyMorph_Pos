@csrf
<div class="space-y-8 divide-y divide-gray-200">
    <!-- SECCIÓN: DATOS DEL INFANTE -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
            Datos del Infante
        </h3>
        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">
            
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                <input type="text" name="nombre_infante" value="{{ old('nombre_infante', $fichaInfante->nombre_infante ?? '') }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600 @error('nombre_infante') border-red-500 @enderror">
                @error('nombre_infante') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Género <span class="text-red-500">*</span></label>
                <select name="sexo_infante" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
                    <option value="Masculino" {{ old('sexo_infante', $fichaInfante->sexo_infante ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="Femenino" {{ old('sexo_infante', $fichaInfante->sexo_infante ?? '') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento <span class="text-red-500">*</span></label>
                <input type="date" name="fecha_nacimiento_infante" value="{{ old('fecha_nacimiento_infante', $fichaInfante->fecha_nacimiento_infante ?? '') }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600 @error('fecha_nacimiento_infante') border-red-500 @enderror">
                @error('fecha_nacimiento_infante') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Fecha de Inscripción</label>
                <input type="date" name="fecha_inscripcion_infante" value="{{ old('fecha_inscripcion_infante', $fichaInfante->fecha_inscripcion_infante ?? date('Y-m-d')) }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>
        </div>
    </div>

    <!-- SECCIÓN: EXÁMENES MÉDICOS Y FÍSICOS -->
    <div class="pt-6 space-y-4">
        <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
            Evaluación Física y Cognitiva
        </h3>
        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Peso (kg)</label>
                <input type="number" step="0.01" name="peso_infante" value="{{ old('peso_infante', $fichaInfante->peso_infante ?? '') }}" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Talla (cm)</label>
                <input type="number" step="0.01" name="talla_infante" value="{{ old('talla_infante', $fichaInfante->talla_infante ?? '') }}" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Prueba de Apetito (%)</label>
                <input type="number" min="0" max="100" name="prueba_apetito_infante" value="{{ old('prueba_apetito_infante', $fichaInfante->prueba_apetito_infante ?? '') }}" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Examen Cognitivo (%)</label>
                <input type="number" min="0" max="100" name="examen_cognitivo_infante" value="{{ old('examen_cognitivo_infante', $fichaInfante->examen_cognitivo_infante ?? '') }}" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>
        </div>
    </div>

    <!-- SECCIÓN: DATOS DEL ENCARGADO Y RESIDENCIA -->
    <div class="pt-6 space-y-4">
        <h3 class="text-lg font-semibold leading-6 text-indigo-700 flex items-center gap-2">
            Información del Encargado y Ubicación
        </h3>
        <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-6">
            
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Nombre del Encargado <span class="text-red-500">*</span></label>
                <input type="text" name="nombre_encargado" value="{{ old('nombre_encargado', $fichaInfante->nombre_encargado ?? '') }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600 @error('nombre_encargado') border-red-500 @enderror">
                @error('nombre_encargado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Parentesco <span class="text-red-500">*</span></label>
                <select name="parentesco_encargado" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
                    @foreach(['madre', 'padre', 'abuelo', 'tio', 'encargado'] as $parentesco)
                        <option value="{{ $parentesco }}" {{ old('parentesco_encargado', $fichaInfante->parentesco_encargado ?? '') == $parentesco ? 'selected' : '' }}>{{ ucfirst($parentesco) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">DPI (13 dígitos)</label>
                <input type="text" maxlength="13" name="dpi_encargado" value="{{ old('dpi_encargado', $fichaInfante->dpi_encargado ?? '') }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600 @error('dpi_encargado') border-red-500 @enderror">
                @error('dpi_encargado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Teléfono (8 dígitos) <span class="text-red-500">*</span></label>
                <input type="text" maxlength="8" name="telefono_encargado" value="{{ old('telefono_encargado', $fichaInfante->telefono_encargado ?? '') }}" 
                    @disabled(isset($readonly) && $readonly)
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600 @error('telefono_encargado') border-red-500 @enderror">
                @error('telefono_encargado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">

                <label for="departamento_residencia_encargado"
                    class="block text-sm font-medium text-gray-700">

                    Departamento
                    <span class="text-red-500">*</span>

                </label>

                <select
                    id="departamento_residencia_encargado"
                    name="departamento_residencia_encargado"

                    @disabled(isset($readonly) && $readonly)

                    class="mt-1 block w-full rounded-md border-gray-300
                        shadow-sm focus:border-indigo-500
                        focus:ring-indigo-500 sm:text-sm p-2 border
                        disabled:bg-gray-100
                        disabled:text-gray-600
                        @error('departamento_residencia_encargado')
                            border-red-500
                        @enderror"
                >

                    <option value="">
                        Seleccione un departamento
                    </option>

                    @foreach($departamentosMunicipios as $departamento => $municipios)

                        <option
                            value="{{ $departamento }}"

                            {{ old(
                                'departamento_residencia_encargado',
                                $fichaInfante->departamento_residencia_encargado ?? ''
                            ) == $departamento ? 'selected' : '' }}
                        >

                            {{ $departamento }}

                        </option>

                    @endforeach

                </select>

                @error('departamento_residencia_encargado')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="sm:col-span-3">

                <label for="municipio_residencia_encargado"
                    class="block text-sm font-medium text-gray-700">

                    Municipio
                    <span class="text-red-500">*</span>

                </label>

                <select
                    id="municipio_residencia_encargado"
                    name="municipio_residencia_encargado"

                    @disabled(isset($readonly) && $readonly)

                    class="mt-1 block w-full rounded-md border-gray-300
                        shadow-sm focus:border-indigo-500
                        focus:ring-indigo-500 sm:text-sm p-2 border
                        disabled:bg-gray-100
                        disabled:text-gray-600
                        @error('municipio_residencia_encargado')
                            border-red-500
                        @enderror"
                >

                    <option value="">
                        Seleccione primero un departamento
                    </option>

                </select>

                @error('municipio_residencia_encargado')
                    <p class="mt-1 text-xs text-red-500">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <div class="sm:col-span-6">
                <label class="block text-sm font-medium text-gray-700">Dirección Residencial (Comunidad, Barrio o Zona) <span class="text-red-500">*</span></label>
                <input type="text" name="direccion_residencia_encargado" value="{{ old('direccion_residencia_encargado', $fichaInfante->direccion_residencia_encargado ?? '') }}" @disabled(isset($readonly) && $readonly) class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-600">
            </div>
        </div>
    </div>
</div>

<div class="pt-5 border-t mt-6 flex justify-end space-x-3">
    <a href="{{ route('ficha-infantes.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancelar</a>
    @if(!$readonly)
    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">Guardar Registro</button>
    @endif
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const datosGuatemala = @json($departamentosMunicipios);
        const departamentoSelect =
            document.getElementById(
                'departamento_residencia_encargado'
            );
        const municipioSelect =
            document.getElementById(
                'municipio_residencia_encargado'
            );
        // Municipio que viene de la base de datos
        const municipioSeleccionado = @json(
            old(
                'municipio_residencia_encargado',
                $fichaInfante->municipio_residencia_encargado ?? ''
            )
        );
        function cargarMunicipios(departamento, municipioSeleccionado = '') {
            // Limpiar municipios
            municipioSelect.innerHTML = '';
            // Opción inicial
            const opcionInicial = document.createElement('option');
            opcionInicial.value = '';
            opcionInicial.textContent = 'Seleccione un municipio';
            municipioSelect.appendChild(opcionInicial);
            // Si no hay departamento
            if (!departamento || !datosGuatemala[departamento]) {
                municipioSelect.disabled = true;
                return;
            }
            // Cargar municipios
            datosGuatemala[departamento].forEach(function (municipio) {
                const option = document.createElement('option');
                option.value = municipio;
                option.textContent = municipio;
                // Seleccionar municipio guardado
                if (municipio === municipioSeleccionado) {
                    option.selected = true;
                }
                municipioSelect.appendChild(option);
            });
            municipioSelect.disabled = false;
        }
        // Cambio de departamento
        departamentoSelect.addEventListener('change', function () {

            cargarMunicipios(this.value);

        });


        // Cargar automáticamente al editar
        if (departamentoSelect.value) {

            cargarMunicipios(
                departamentoSelect.value,
                municipioSeleccionado
            );

        } else {

            municipioSelect.disabled = true;

        }

    });

</script>

