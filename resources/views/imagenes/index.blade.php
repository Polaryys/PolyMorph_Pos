<x-app-layout>
    <!-- Encabezado y acción -->
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('imagenes.create') }}" wire:navigate
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Cargar imágenes
        </a>
    </div>

    <!-- Mensajes -->
    @if (session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- imagenes -->
    <div class="bg-white">
        @forelse ($imagenes as $imagen)
            @if ($loop->first)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4">
            @endif

            <div class="min-w-0 border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                <!-- vista previo iamgen -->
                <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/imagenes/' . $imagen->nombre_archivo) }}" alt="{{ $imagen->nombre }}"
                        loading="lazy" class="w-full h-full object-contain">
                </div>

                <!-- info  y acciones -->
                <div class="p-3 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 truncate" title="{{ $imagen->nombre }}">
                        {{ $imagen->nombre }}
                    </h3>

                    <p class="text-xs text-gray-500">
                        {{ $imagen->ancho }} × {{ $imagen->alto }} px
                        ·
                        {{ number_format($imagen->peso_archivo / 1024, 1) }} KB
                    </p>

                    <div class="flex items-center justify-between gap-2  pt-2 border-t border-gray-100">
                        <button type="button"
                            @click="$dispatch('abrir-edicion-imagen', {
                                nombre: {{ Js::from($imagen->nombre) }},
                                src: {{ Js::from(asset('storage/imagenes/' . $imagen->nombre_archivo)) }},
                                ancho: {{ Js::from($imagen->ancho) }},
                                alto: {{ Js::from($imagen->alto) }},
                                peso: {{ Js::from(number_format($imagen->peso_archivo / 1024, 1)) }},
                                url: {{ Js::from(route('imagenes.update', $imagen)) }},
                                urlEliminar: {{ Js::from(route('imagenes.destroy', $imagen)) }}
                            })"
                            class="text-sm font-medium text-indigo-600 transition hover:text-indigo-900">
                            Editar
                        </button>
                    </div>
                </div>
            </div>

            @if ($loop->last)
    </div>
    @endif
@empty
    <div class="py-12 px-4 text-center">
        <div class="flex justify-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <circle cx="8.5" cy="8.5" r="1.5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21" />
            </svg>
        </div>

        <p class="text-sm font-medium text-gray-600">
            No hay imágenes registradas.
        </p>

        <p class="mt-1 text-sm text-gray-400">
            Registra una imagen para que aparezca en este espacio.
        </p>
    </div>
    @endforelse

    <!-- Paginación -->
    @if ($imagenes->hasPages())
        <div class="mt-4 pt-3 border-t border-gray-200">
            {{ $imagenes->links() }}
        </div>
    @endif
    </div>


    <!-- Modal  edicion -->
    @include('imagenes.partials.edit')



</x-app-layout>
