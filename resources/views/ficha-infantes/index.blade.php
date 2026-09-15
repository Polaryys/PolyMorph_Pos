<x-app-layout>
    <x-slot name="module_nav">
        @include('layouts.navigation')
    </x-slot>

    <div class="space-y-6">


    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Fichas Infantes
                    </h1>
                    <p class="text-sm text-gray-500">
                        Gestión y seguimiento de infantes registrados
                    </p>
                </div>
            </div>
        </div>

        <a href="{{ route('ficha-infantes.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3
                  text-sm font-semibold text-white shadow-sm transition
                  hover:bg-indigo-700 hover:shadow-md focus:outline-none focus:ring-2
                  focus:ring-indigo-500 focus:ring-offset-2">

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4"/>
            </svg>

            Registrar infante
        </a>
    </div>


    {{-- ALERTA --}}
    @if(session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-emerald-200
                    bg-emerald-50 p-4 text-emerald-800 shadow-sm">

            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <div>
                <p class="font-semibold">Operación exitosa</p>
                <p class="text-sm text-emerald-700">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif





    {{-- CONTENEDOR PRINCIPAL --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        {{-- TABLA --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Infante
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Sexo
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Encargado
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Ubicación
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Acciones
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($infantes as $infante)
                        <tr class="group transition hover:bg-indigo-50/40">
                            {{-- INFANTE --}}
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full
                                                bg-indigo-100 font-bold text-indigo-700">

                                        {{ strtoupper(substr($infante->nombre_infante, 0, 1)) }}

                                    </div>
                                    <div>

                                        <div class="font-semibold text-gray-900">
                                            {{ $infante->nombre_infante }}
                                        </div>

                                        <div class="mt-0.5 text-xs text-gray-500">
                                            Código:
                                            <span class="font-mono font-semibold text-indigo-600">
                                                {{ $infante->codigo_infante }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            {{-- SEXO --}}
                            <td class="whitespace-nowrap px-6 py-5">

                                @if($infante->sexo_infante === 'Masculino')
                                    <span class="inline-flex items-center gap-1.5 rounded-full
                                                 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Masculino
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full
                                                 bg-pink-50 px-3 py-1 text-xs font-semibold text-pink-700">
                                        Femenino
                                    </span>
                                @endif
                            </td>

                            {{-- ENCARGADO --}}
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="font-medium text-gray-900">
                                    {{ $infante->nombre_encargado }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ ucfirst($infante->parentesco_encargado) }}
                                </div>
                                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 5a2 2 0 012-2h3.28a2 2 0 011.94 1.515L11 7a2 2 0 01-.45 1.86l-1.27 1.27a16 16 0 006.59 6.59l1.27-1.27A2 2 0 0119 15l2.485.78A2 2 0 0123 17.72V21a2 2 0 01-2 2C10.611 23 1 13.389 1 3a2 2 0 012-2z"/>
                                    </svg>
                                    {{ $infante->telefono_encargado }}
                                </div>
                            </td>
                            {{-- UBICACIÓN --}}
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-5 w-5 text-gray-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ $infante->municipio_residencia_encargado }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $infante->departamento_residencia_encargado }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- ACCIONES --}}
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- VER --}}
                                    <a href="{{ route('ficha-infantes.show', $infante) }}"
                                       title="Ver ficha"
                                       class="flex h-9 w-9 items-center justify-center rounded-lg
                                              border border-gray-200 text-gray-500 transition
                                              hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600">

                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>

                                    </a>


                                    {{-- EDITAR --}}
                                    <a href="{{ route('ficha-infantes.edit', $infante) }}"
                                       title="Editar ficha"
                                       class="flex h-9 w-9 items-center justify-center rounded-lg
                                              border border-gray-200 text-gray-500 transition
                                              hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">

                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>

                                    </a>


                                    {{-- ELIMINAR --}}
                                    <form action="{{ route('ficha-infantes.destroy', $infante) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar permanentemente esta ficha?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                title="Eliminar ficha"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg
                                                       border border-gray-200 text-gray-500 transition
                                                       hover:border-red-200 hover:bg-red-50 hover:text-red-600">

                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                    <svg class="h-8 w-8 text-gray-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 10-6 0"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                                    No hay infantes registrados
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Comienza registrando el primer infante en el sistema.
                                </p>
                                <a href="{{ route('ficha-infantes.create') }}"
                                   class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600
                                          px-4 py-2.5 text-sm font-semibold text-white
                                          hover:bg-indigo-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Registrar primer infante
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- PAGINACIÓN --}}
        @if($infantes->hasPages())

            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">

                <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">

                    <p class="text-sm text-gray-500">
                        Mostrando
                        <span class="font-semibold text-gray-700">
                            {{ $infantes->firstItem() ?? 0 }}
                        </span>
                        -
                        <span class="font-semibold text-gray-700">
                            {{ $infantes->lastItem() ?? 0 }}
                        </span>
                        de
                        <span class="font-semibold text-gray-700">
                            {{ $infantes->total() }}
                        </span>
                        registros
                    </p>

                    <div>
                        {{ $infantes->links() }}
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>


</x-app-layout>
