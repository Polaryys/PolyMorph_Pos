<x-app-layout>

    <div class="space-y-4">

        @if (session('success'))
            <p class="text-sm font-medium text-green-600">
                {{ session('success') }}
            </p>
            
        @endif

    </div>

    <!-- form editar -->
    <form id="edit-user-form" method="POST" action="{{ route('usuarios.update', $usuario) }}"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="flex items-center justify-left gap-3">

            <a href="{{ route('usuarios.index') }}" wire:navigate
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition">
                Volver
            </a>

            <button type="submit"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white
                       text-sm font-medium rounded-md shadow-sm transition">
                Guardar Cambios
            </button>

        </div>

        <div class="space-y-6">

            <!-- imagen -->
            <div class="flex flex-col items-center justify-center space-y-3 mt-2">

                <div class="relative h-32 w-32">

                    <img id="avatar-preview"
                        src="{{ $usuario->imagen_id && $usuario->imagen
                            ? asset('storage/imagenes/' . $usuario->imagen->nombre_archivo)
                            : ($usuario->foto
                                ? asset('storage/' . $usuario->foto)
                                : asset('images/default-avatar.png')) }}"
                        alt="Foto de perfil"
                        class="h-32 w-32S rounded-full border-2 border-indigo-100 object-cover shadow-sm">

                    <!-- abrir galería -->
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                        title="Seleccionar imagen de la galería" aria-label="Seleccionar imagen de la galería"
                        class="absolute bottom-0 right-0 rounded-full bg-indigo-600 p-2 text-white shadow-md transition hover:bg-indigo-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>

                </div>

                <input type="hidden" id="imagen_id" name="imagen_id"
                    value="{{ old('imagen_id', $usuario->imagen_id) }}">

                @error('imagen_id')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            @include('usuarios.partials.edit-user-info')

            @include('usuarios.partials.edit-user-credentials')

        </div>

    </form>

    <script>
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            console.log('Imagen recibida:', imagen);

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            document.getElementById('imagen_id').value = imagen.id;

            document.getElementById('avatar-preview').src = imagen.url;

        });
    </script>


</x-app-layout>
