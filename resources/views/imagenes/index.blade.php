<x-app-layout>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">

        {{-- search imagen --}}
        <form action="{{ route('imagenes.index') }}" method="GET" class="flex flex-1 items-center gap-2">

            <input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar imagen por nombre..."
                class="w-full max-w-md  border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                Buscar
            </button>

            @if (request()->filled('buscar'))
                <a href="{{ route('imagenes.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900">
                    Limpiar
                </a>
            @endif
        </form>

        {{-- subir imagenes --}}
        <a href="{{ route('imagenes.create') }}" wire:navigate
            class="shrink-0 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition">
            + Cargar imágenes
        </a>

    </div>

    {{-- Mensajes --}}
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

    {{-- Imágenes --}}
    <div class="bg-white">
        @forelse ($imagenes as $imagen)
            @if ($loop->first)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4">
            @endif

            <div class="min-w-0 border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                {{-- Vista previa --}}
                <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/imagenes/' . $imagen->nombre_archivo) }}" alt="{{ $imagen->nombre }}"
                        loading="lazy" class="w-full h-full object-contain">
                </div>

                {{-- Info imageny edicion --}}
                <div class="p-3 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 truncate" title="{{ $imagen->nombre }}">
                        {{ $imagen->nombre }}
                    </h3>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <p class="text-xs text-gray-500 min-w-0" data-info-imagen
                            data-src="{{ asset('storage/imagenes/' . $imagen->nombre_archivo) }}">
                            Calculando dimensiones y peso...
                        </p>

                        <button type="button"
                            @click="
                            const info = $el.closest('.min-w-0').querySelector('[data-info-imagen]');
                            $dispatch('abrir-edicion-imagen', {
                                nombre: {{ Js::from($imagen->nombre) }},
                                src: {{ Js::from(asset('storage/imagenes/' . $imagen->nombre_archivo)) }},
                                ancho: info.dataset.ancho || null,
                                alto: info.dataset.alto || null,
                                peso: info.dataset.peso || null,
                                url: {{ Js::from(route('imagenes.update', $imagen)) }},
                                urlEliminar: {{ Js::from(route('imagenes.destroy', $imagen)) }}
                            })
                        "
                            class="shrink-0 text-sm font-medium text-indigo-600 transition hover:text-indigo-900">
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
            @if (request()->filled('buscar'))
                No se encontraron imágenes que coincidan con "{{ request('buscar') }}".
            @else
                No hay imágenes registradas.
            @endif
        </p>

        <p class="mt-1 text-sm text-gray-400">
            @if (request()->filled('buscar'))
                Prueba con otro nombre o limpia la búsqueda.
            @else
                Registra una imagen para que aparezca en este espacio.
            @endif
        </p>
    </div>
    @endforelse

    {{-- Paginación --}}
    @if ($imagenes->hasPages())
        <div class="mt-4 pt-3 border-t border-gray-200">
            {{ $imagenes->links() }}
        </div>
    @endif
    </div>


    <script>
        async function cargarInfoImagenes() {
            const elementos = document.querySelectorAll('[data-info-imagen]');

            elementos.forEach((elemento) => {
                if (elemento.dataset.procesado === 'true') return;
                elemento.dataset.procesado = 'true';

                const src = elemento.dataset.src;
                const imagen = new Image();

                imagen.onload = async () => {
                    const ancho = imagen.naturalWidth;
                    const alto = imagen.naturalHeight;
                    let peso = null;

                    try {
                        const respuesta = await fetch(src, {
                            method: 'HEAD'
                        });
                        const bytes = Number(respuesta.headers.get('content-length'));

                        if (respuesta.ok && Number.isFinite(bytes) && bytes > 0) {
                            peso = (bytes / 1024).toFixed(1);
                        }
                    } catch (error) {
                        // El peso puede no estar disponible en el servidor.
                    }

                    elemento.textContent =
                        `${ancho} × ${alto} px · ${peso !== null ? peso + ' KB' : 'Peso no disponible'}`;

                    elemento.dataset.ancho = ancho;
                    elemento.dataset.alto = alto;
                    elemento.dataset.peso = peso ?? '';
                };

                imagen.onerror = () => {
                    elemento.textContent = 'No se pudo cargar la información';
                };

                imagen.src = src;
            });
        }

        document.addEventListener('DOMContentLoaded', cargarInfoImagenes);
        document.addEventListener('livewire:navigated', cargarInfoImagenes);
    </script>


    {{-- Modal de edición --}}
    @include('imagenes.partials.edit')
</x-app-layout>
