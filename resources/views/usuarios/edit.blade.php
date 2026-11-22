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



        <div class="space-y-6">

            <!-- imagen -->
            <div class="flex flex-col items-center justify-center space-y-3 mt-2">

                <div class="relative h-32 w-32">
                    <!-- Quitar imagen -->
                    <button type="button" id="btn-quitar-imagen" title="Quitar imagen" aria-label="Quitar imagen"
                        class="absolute left-0 top-0 rounded-full bg-red-500 p-1.5 text-white shadow-md transition hover:bg-red-600">

                        <x-lucide-x class="h-4 w-4" />
                    </button>
                    <input type="hidden" name="quitar_imagen" id="quitar_imagen" value="0">

                    @if ($usuario->imagen_id && $usuario->imagen)
                        <img id="avatar-preview"
                            src="{{ asset('storage/imagenes/' . $usuario->imagen->nombre_archivo) }}"
                            alt="Foto de perfil"
                            class="h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm">
                    @elseif ($usuario->foto)
                        <img id="avatar-preview" src="{{ asset('storage/' . $usuario->foto) }}" alt="Foto de perfil"
                            class="h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm">
                    @else
                        <div id="avatar-preview"
                            class="flex h-32 w-32 items-center justify-center rounded-full border-2 border-app-border-strong bg-app-background text-app-text-muted">
                            <x-lucide-user-round class="h-16 w-16" />
                        </div>
                    @endif

                    <!-- Abrir galería -->
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('abrir-selector-imagen'))"
                        title="Seleccionar imagen de la galería" aria-label="Seleccionar imagen de la galería"
                        class="absolute bottom-0 right-0 rounded-full bg-app-primary-600 p-2 text-white shadow-md transition hover:bg-indigo-700">

                        <x-lucide-camera class="h-4 w-4" />

                    </button>

                </div>

                <input type="hidden" id="imagen_id" name="imagen_id"
                    value="{{ old('imagen_id', $usuario->imagen_id) }}">

                <x-input-error :messages="$errors->get('imagen_id')" />

            </div>

            @include('usuarios.partials.edit-user-info')

            @include('usuarios.partials.edit-user-credentials')

        </div>
        <div class="mt-5 flex items-center justify-end gap-3 pb-6">

            <x-secondary-link href="{{ route('usuarios.index') }}" wire:navigate>
                Volver
            </x-secondary-link>

            <x-primary-button>
                Guardar Cambios
            </x-primary-button>

        </div>

    </form>

    <script>
        // Mostrar imagen seleccionada
        window.addEventListener('imagen-seleccionada', function(event) {
            const imagen = event.detail;

            if (!imagen || imagen.id == null || !imagen.url) {
                console.error('La imagen recibida no contiene los datos esperados.');
                return;
            }

            document.getElementById('imagen_id').value = imagen.id;
            document.getElementById('quitar_imagen').value = '0';

            const avatar = document.getElementById('avatar-preview');

            // Crear o actualizar imagen
            if (avatar.tagName === 'IMG') {
                avatar.src = imagen.url;
            } else {
                const nuevaImagen = document.createElement('img');

                nuevaImagen.id = 'avatar-preview';
                nuevaImagen.src = imagen.url;
                nuevaImagen.alt = 'Foto de perfil';
                nuevaImagen.className =
                    'h-32 w-32 rounded-full border-2 border-app-border-strong object-cover shadow-sm';

                avatar.replaceWith(nuevaImagen);
            }
        });

        // Quitar imagen y restaurar icono
        document.getElementById('btn-quitar-imagen').addEventListener('click', function() {
            const avatar = document.getElementById('avatar-preview');

            const icono = document.createElement('div');

            icono.id = 'avatar-preview';
            icono.className =
                'flex h-32 w-32 items-center justify-center rounded-full border-2 border-app-border-strong bg-app-background text-app-text-muted';

            icono.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                width="64" height="64" viewBox="0 0 24 24"
                fill="none" stroke="currentColor"
                stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="8" r="5"/>
                <path d="M20 21a8 8 0 0 0-16 0"/>
            </svg>
        `;

            avatar.replaceWith(icono);

            document.getElementById('imagen_id').value = '';
            document.getElementById('quitar_imagen').value = '1';
        });
    </script>

</x-app-layout>
