
            @csrf
            {{-- INFANTE --}}

            <div>
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    1. Información del infante
                </h2>
          <!-- Contenedor de las Tarjetas (Grid de 3x2) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
                        <!-- FILA UP - Card 1: Nombre -->
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900">{{ $infante->nombre_infante }}</p>                            
                                <p class="text-base font-mono font-bold text-indigo-600">{{ $infante->codigo_infante }}</p>
                            </div>
                        </div>
                        <!-- FILA UP - Card 3: Fecha de Nacimiento -->
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nacimiento</h3>
                                <p class="text-base font-bold text-gray-900">{{ $infante->fecha_nacimiento_infante ?? 'No registrada' }}</p>
                            </div>
                        </div>

                        <!-- FILA DOWN - Card 5: Tutor / Responsable -->
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Peso</h3>
                                <p class="text-base font-bold text-gray-900">{{ $infante->peso_infante ?? 'No asignado' }}</p>
                            </div>
                        </div>

                        <!-- FILA DOWN - Card 6: Estado / Estatus -->
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Talla</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $infante->talla_infante ?? 'Activo' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Preba Apetito</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $infante->prueba_apetito_infante ?? 0 }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ex. Cognitivo</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $infante->examen_cognitivo_infante ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
            </div>
            {{-- PESO --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    2. Peso
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">

                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Peso actual (kg) *
                        </label>

                        <input type="number"
                               name="peso_actual_control"
                               step="0.01"
                               min="0"
                               value="{{ old('peso_actual_control') }}"
                               required
                               class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Peso ideal (kg)
                        </label>

                        <input type="number"
                               name="peso_ideal_control"
                               step="0.01"
                               min="0"
                               value="{{ old('peso_ideal_control') }}"
                               class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Indicador P/E
                        </label>

                        <select name="indicador_pe_control"
                                class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                Seleccione
                            </option>

                            @foreach([
                                'PB Severo',
                                'PB Moderado',
                                'Riesgo BP',
                                'Normal',
                                'Sobrepeso',
                                'Obesidad'
                            ] as $opcion)

                                <option value="{{ $opcion }}"
                                    {{ old('indicador_pe_control') == $opcion ? 'selected' : '' }}>

                                    {{ $opcion }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>


            {{-- TALLA --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    3. Talla
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">

                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Talla actual *
                        </label>

                        <input type="number"
                               name="talla_actual_control"
                               step="0.01"
                               min="0"
                               value="{{ old('talla_actual_control') }}"
                               required
                               class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Talla ideal
                        </label>

                        <input type="number"
                               name="talla_ideal_control"
                               step="0.01"
                               min="0"
                               value="{{ old('talla_ideal_control') }}"
                               class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                    </div>


                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Indicador T/E
                        </label>

                        <select name="indicador_te_control"
                                class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                Seleccione
                            </option>

                            @foreach([
                                'RC. Severo',
                                'RC. Moderado',
                                'Riesgo RC',
                                'C. Adecuado',
                                'Riesgo DC',
                                'DC. Moderada',
                                'DC. Severa'
                            ] as $opcion)

                                <option value="{{ $opcion }}"
                                    {{ old('indicador_te_control') == $opcion ? 'selected' : '' }}>

                                    {{ $opcion }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>


            {{-- P/T --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    4. Indicador Peso / Talla
                </h2>

                <div class="mt-5">

                    <label class="text-sm font-semibold text-gray-700">
                        Indicador P/T
                    </label>

                    <select name="indicador_pt_control"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Seleccione
                        </option>

                        @foreach([
                            'DA Severa',
                            'DA Moderada',
                            'Riesgo DA',
                            'Normal',
                            'Riesgo SP',
                            'Sobrepeso',
                            'Obesidad'
                        ] as $opcion)

                            <option value="{{ $opcion }}"
                                {{ old('indicador_pt_control') == $opcion ? 'selected' : '' }}>

                                {{ $opcion }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- EVOLUCIÓN --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    5. Evolución y producto
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Evolución *
                        </label>

                        <select name="evolucion_control"
                                required
                                class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                Seleccione
                            </option>

                            @foreach([
                                'Mejoria',
                                'Normal',
                                'Sin mejora',
                                'Desnutricion'
                            ] as $opcion)

                                <option value="{{ $opcion }}"
                                    {{ old('evolucion_control') == $opcion ? 'selected' : '' }}>

                                    {{ $opcion }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Producto
                        </label>

                        <select name="producto_control"
                                class="mt-2 w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                            <option value="">
                                Sin producto
                            </option>

                            <option value="Leche"
                                {{ old('producto_control') == 'Leche' ? 'selected' : '' }}>
                                Leche
                            </option>

                            <option value="Incaparina"
                                {{ old('producto_control') == 'Incaparina' ? 'selected' : '' }}>
                                Incaparina
                            </option>

                            <option value="Atol"
                                {{ old('producto_control') == 'Atol' ? 'selected' : '' }}>
                                Atol
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- BOTONES --}}
            <div class="flex justify-end gap-3">

                <a href="{{ route('control-infantes.index') }}"
                   class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold
                          text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold
                               text-white shadow-sm hover:bg-indigo-700">
                    Guardar control
                </button>
            </div>
      
            </div>