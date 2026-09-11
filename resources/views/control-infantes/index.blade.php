<x-app-layout>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                        <svg class="h-7 w-7"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 016 0M9 5h6"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Controles de Infantes
                        </h1>

                        <p class="text-sm text-gray-500">
                            Seguimiento del crecimiento y estado nutricional
                        </p>
                    </div>

                </div>
            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MENSAJE --}}
        {{-- ========================================================= --}}
        @if(session('success'))

            <div class="flex items-start gap-3 rounded-xl border border-emerald-200
                        bg-emerald-50 p-4 text-emerald-800">

                <div>
                    <p class="font-semibold">
                        Operación exitosa
                    </p>

                    <p class="text-sm">
                        {{ session('success') }}
                    </p>
                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- SELECTOR DE INFANTE --}}
        {{-- ========================================================= --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <form method="GET"
                  action="{{ route('control-infantes.index') }}">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    {{-- SELECT --}}
                    <div class="md:col-span-9">
                        <label for="codigo_infante"
                               class="mb-2 block text-sm font-semibold text-gray-700">
                            Seleccionar Infante
                        </label>
                        <select name="id"
                                id="id"
                                class="w-full rounded-xl border-gray-300
                                       focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">
                                -- Seleccione un infante --
                            </option>

                            @foreach($infantes as $infante)

                                <option value="{{ $infante->id }}"
                                    {{ $id == $infante->id ? 'selected' : '' }}>

                                    {{ $infante->nombre_infante }}
                                    -
                                    {{ $infante->codigo_infante }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BOTÓN BUSCAR --}}
                    <div class="md:col-span-3 flex items-end">

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2
                                       rounded-xl bg-indigo-600 px-5 py-3
                                       text-sm font-semibold text-white
                                       shadow-sm transition
                                       hover:bg-indigo-700
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:ring-offset-2">

                            <svg class="h-5 w-5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="m21 21-4.35-4.35m2.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/>

                            </svg>

                            Buscar

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- RESULTADO --}}
        {{-- ========================================================= --}}

        @if($id)

            {{-- ===================================================== --}}
            {{-- SI EXISTEN CONTROLES --}}
            {{-- ===================================================== --}}
            @if($controles->count())

                @php
                    $infante = $controles->first()->infante;
                @endphp


                <div class="overflow-hidden rounded-2xl border border-gray-200
                            bg-white shadow-sm">

                    {{-- ================================================= --}}
                    {{-- CABECERA DEL RESULTADO --}}
                    {{-- ================================================= --}}

                   <div class="flex flex-col gap-6 border-b border-gray-200 bg-gray-50 p-5 md:flex-row md:items-center md:justify-between">
    
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

                    <!-- Botón de Acción a la Derecha -->
                         {{-- AGREGAR NUEVO CONTROL --}}
                        <a href="{{ route('control-infantes.create', [
                            'id' => $infante->id
                        ]) }}"
                           class="inline-flex items-center justify-center gap-2
                                  rounded-xl bg-indigo-600 px-5 py-3
                                  text-sm font-semibold text-white
                                  shadow-sm transition
                                  hover:bg-indigo-700">

                            <svg class="h-5 w-5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Nuevo control
                        </a>

                </div>

















               
                        <!-- Contenedor de las mini cards en horizontal -->
                  
                    

                    {{-- ================================================= --}}
                    {{-- TABLA DE CONTROLES --}}
                    {{-- ================================================= --}}
                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-white">

                                <tr>
                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Control
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Edad
                                    </th>

                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Peso
                                    </th>

                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Talla
                                    </th>

                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Evolución
                                    </th>

                                    <th class="px-6 py-4 text-center text-xs
                                               font-semibold uppercase
                                               tracking-wider text-gray-500">
                                        Acciones
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100">

                                @foreach($controles as $control)

                                    <tr class="transition hover:bg-indigo-50/40">

                                        {{-- ================================= --}}
                                        {{-- CONTROL --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5 text-center">

                                            <span class="inline-flex items-center
                                                         rounded-full bg-indigo-100
                                                         px-4 py-2 text-xs
                                                         font-bold text-indigo-700">

                                                #{{ $control->numero_control }}

                                            </span>

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- EDAD --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5 text-center">

                                            <div class="text-sm font-semibold text-gray-700">

                                                {{ $control->anios_edad_control }}
                                                años

                                            </div>

                                            <div class="text-xs text-gray-400">

                                                {{ $control->meses_edad_control }}
                                                meses

                                                {{ $control->dia_edad_control }}
                                                días

                                            </div>

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- PESO --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5 text-center">

                                            <div class="font-semibold text-gray-800">

                                                {{ number_format(
                                                    $control->peso_actual_control,
                                                    2
                                                ) }}
                                                kg

                                            </div>

                                            @if($control->indicador_pe_control)

                                                <span class="text-xs text-gray-400">

                                                    {{ $control->indicador_pe_control }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- TALLA --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5 text-center">

                                            <div class="font-semibold text-gray-800">

                                                {{ number_format(
                                                    $control->talla_actual_control,
                                                    2
                                                ) }}

                                            </div>

                                            @if($control->indicador_te_control)

                                                <span class="text-xs text-gray-400">

                                                    {{ $control->indicador_te_control }}

                                                </span>

                                            @endif

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- EVOLUCIÓN --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5 text-center">

                                            @php

                                                $claseEvolucion = match(
                                                    $control->evolucion_control
                                                ) {

                                                    'Mejoria' =>
                                                        'bg-emerald-50 text-emerald-700',

                                                    'Normal' =>
                                                        'bg-blue-50 text-blue-700',

                                                    'Sin mejora' =>
                                                        'bg-amber-50 text-amber-700',

                                                    'Desnutricion' =>
                                                        'bg-red-50 text-red-700',

                                                    default =>
                                                        'bg-gray-50 text-gray-700',

                                                };

                                            @endphp


                                            <span class="inline-flex rounded-full
                                                         px-3 py-1 text-xs
                                                         font-semibold
                                                         {{ $claseEvolucion }}">

                                                {{ $control->evolucion_control }}

                                            </span>

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- ACCIONES --}}
                                        {{-- ================================= --}}
                                        <td class="px-6 py-5">

                                            <div class="flex items-center
                                                        justify-center gap-2">

                                                {{-- VER --}}
                                                <a href="{{ route(
                                                    'control-infantes.show',
                                                    $control
                                                ) }}"
                                                   title="Ver control"
                                                   class="flex h-9 w-9 items-center
                                                          justify-center rounded-lg
                                                          border border-gray-200
                                                          text-gray-500 transition
                                                          hover:border-indigo-200
                                                          hover:bg-indigo-50
                                                          hover:text-indigo-600">

                                                    <svg class="h-4 w-4"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         viewBox="0 0 24 24">

                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>

                                                    </svg>

                                                </a>


                                                {{-- EDITAR --}}
                                                <a href="{{ route(
                                                    'control-infantes.edit',
                                                    $control
                                                ) }}"
                                                   title="Editar control"
                                                   class="flex h-9 w-9 items-center
                                                          justify-center rounded-lg
                                                          border border-gray-200
                                                          text-gray-500 transition
                                                          hover:border-amber-200
                                                          hover:bg-amber-50
                                                          hover:text-amber-600">

                                                    <svg class="h-4 w-4"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         viewBox="0 0 24 24">

                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                                                    </svg>

                                                </a>


                                                {{-- ELIMINAR --}}
                                                <form action="{{ route(
                                                    'control-infantes.destroy',
                                                    $control
                                                ) }}"
                                                      method="POST"
                                                      onsubmit="return confirm(
                                                        '¿Seguro que deseas eliminar este control?'
                                                      );">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button type="submit"
                                                            title="Eliminar control"
                                                            class="flex h-9 w-9
                                                                   items-center
                                                                   justify-center
                                                                   rounded-lg
                                                                   border
                                                                   border-gray-200
                                                                   text-gray-500
                                                                   transition
                                                                   hover:border-red-200
                                                                   hover:bg-red-50
                                                                   hover:text-red-600">

                                                        <svg class="h-4 w-4"
                                                             fill="none"
                                                             stroke="currentColor"
                                                             viewBox="0 0 24 24">

                                                            <path stroke-linecap="round"
                                                                  stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>

                                                        </svg>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- ================================================= --}}
                    {{-- PIE DE TABLA --}}
                    {{-- ================================================= --}}
                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">

                        <div class="flex items-center justify-between">

                            <p class="text-sm text-gray-500">

                                Total de controles:

                                <span class="font-bold text-gray-800">
                                    {{ $controles->count() }}
                                </span>

                            </p>

                        </div>

                    </div>

                </div>


            @else

                {{-- ===================================================== --}}
                {{-- INFANTE SIN CONTROLES --}}
                {{-- ===================================================== --}}
                <div class="rounded-2xl border border-amber-200
                            bg-amber-50 p-8 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center
                                justify-center rounded-full bg-amber-100
                                text-amber-600">

                        <svg class="h-7 w-7"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.71 3h17.64a2 2 0 001.71-3l-8.82-15a2 2 0 00-3.42 0z"/>

                        </svg>

                    </div>


                    <h3 class="mt-4 text-lg font-bold text-gray-900">
                        No hay controles registrados
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Este infante todavía no tiene controles registrados.
                    </p>


                    {{-- NUEVO CONTROL --}}
                    <a href="{{ route('control-infantes.create', [
                        'id' => $id
                    ]) }}"
                       class="mt-5 inline-flex items-center gap-2
                              rounded-xl bg-indigo-600 px-5 py-3
                              text-sm font-semibold text-white
                              hover:bg-indigo-700">

                        <svg class="h-5 w-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 4v16m8-8H4"/>

                        </svg>

                        Agregar primer control

                    </a>

                </div>

            @endif


        @else

            {{-- ========================================================= --}}
            {{-- NINGÚN INFANTE SELECCIONADO --}}
            {{-- ========================================================= --}}
            <div class="rounded-2xl border border-gray-200
                        bg-white p-12 text-center shadow-sm">

                <div class="mx-auto flex h-16 w-16 items-center
                            justify-center rounded-full bg-indigo-100
                            text-indigo-600">

                    <svg class="h-8 w-8"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>

                    </svg>

                </div>


                <h3 class="mt-5 text-lg font-bold text-gray-900">

                    Seleccione un infante

                </h3>

                <p class="mt-1 text-sm text-gray-500">

                    Seleccione un infante de la lista y presione
                    <strong>Buscar</strong> para visualizar sus controles.

                </p>

            </div>

        @endif

    </div>

</x-app-layout>