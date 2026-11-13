<template x-teleport="body">
    <div x-cloak x-show="modalCargaAbierto" x-transition.opacity
        class="fixed inset-0 z-100 flex items-center justify-center bg-black/40 p-4"
        @click.self="cerrarModalCarga()">
        <div x-show="modalCargaAbierto" x-transition
            class="relative w-full max-w-md overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-xl"
            role="dialog" aria-modal="true" aria-labelledby="titulo-modal-carga-imagenes" @click.stop>
            <h2 id="titulo-modal-carga-imagenes"
                class="mb-5 border-b border-gray-200 pb-3 text-lg font-semibold text-indigo-600">
                Cargar imágenes
            </h2>

            <form action="{{ route('imagenes.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4" @submit="iniciarCarga($event)">
                @csrf
                <input type="hidden" name="modal_carga_imagenes" value="1">

                @if ($errors->any() && old('modal_carga_imagenes'))
                    <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">
                        <p class="font-medium">No se pudieron cargar las imágenes:</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="archivos" class="mb-1 block text-sm font-medium text-gray-700">
                        Seleccionar imágenes
                    </label>
                    <input x-ref="archivosCarga" type="file" id="archivos" name="archivos[]"
                        accept="image/jpeg,image/png,image/webp,image/gif" multiple required
                        @change="archivosSeleccionados = $event.target.files.length"
                        class="block w-full rounded-md border border-gray-300 text-sm text-gray-600
                            file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5
                            file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-2 text-xs text-gray-500">
                        Formatos permitidos: JPG, JPEG, PNG, WEBP y GIF. Tamaño máximo: 2 MB por imagen.
                    </p>
                    <p x-show="archivosSeleccionados > 0" class="mt-1 text-xs font-medium text-gray-600">
                        <span x-text="archivosSeleccionados"></span> archivo(s) seleccionado(s).
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <button type="button" @click="cerrarModalCarga()" :disabled="cargandoImagenes"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="cargandoImagenes || archivosSeleccionados === 0"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <span x-text="cargandoImagenes ? 'Cargando...' : 'Guardar imágenes'"></span>
                    </button>
                </div>
            </form>

            <div x-cloak x-show="cargandoImagenes" x-transition.opacity
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/95 p-6"
                role="status" aria-live="polite">
                <div class="w-full max-w-xs text-center">
                    <svg class="mx-auto h-12 w-12 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" stroke="currentColor" stroke-linecap="round"
                            stroke-width="4" d="M12 2a10 10 0 0 1 10 10"></path>
                    </svg>
                    <p class="mt-4 text-base font-semibold text-gray-900">Procesando imágenes...</p>
                    <p class="mt-2 text-sm text-gray-600">
                        No cierres esta ventana ni vuelvas a presionar el botón. Espera a que termine la carga.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
