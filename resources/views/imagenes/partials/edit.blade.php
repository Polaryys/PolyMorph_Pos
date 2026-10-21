<template x-teleport="body">
<div x-data="{
    abierto: false,
    imagen: {},
    nombre: '',
    urlActualizar: '',
    urlEliminar: '',

    abrir(datos) {
        this.imagen = datos;
        this.nombre = datos.nombre;
        this.urlActualizar = datos.url;
        this.urlEliminar = datos.urlEliminar;
        this.abierto = true;
    }
}" @abrir-edicion-imagen.window="abrir($event.detail)" @keydown.escape.window="abierto = false">
    <div x-cloak x-show="abierto" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4" @click.self="abierto = false">
        <div x-show="abierto" x-transition class="w-full max-w-4xl overflow-hidden  bg-white shadow-2xl">
            {{-- Encabezado --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Editar imagen
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifica el nombre o elimina la imagen.
                    </p>
                </div>

                <button type="button" @click="abierto = false"
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Cerrar modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Contenido --}}
            <div class="grid grid-cols-1 gap-6 p-5 md:grid-cols-5">

                <div
                    class="flex min-h-64 items-center justify-center overflow-hidden border border-gray-200 p-3 md:col-span-3 md:min-h-[400px]">
                    <img :src="imagen.src" :alt="nombre" class="max-h-[400px] w-full object-contain">
                </div>

                <div class="flex flex-col md:col-span-2">

                    {{-- Información de la imagen --}}
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Información de la imagen
                        </h3>

                        <div class="mt-4 space-y-4 rounded-xl border border-gray-200 bg-gray-50 p-4">

                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500">Nombre actual</p>
                                <p class="mt-1 break-words text-sm font-semibold text-gray-900" x-text="imagen.nombre">
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-3">

                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-500">Dimensiones</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">
                                        <span x-text="imagen.ancho ?? 'Calculando...'"></span>
                                        ×
                                        <span x-text="imagen.alto ?? 'Calculando...'"></span>
                                        <span class="text-xs text-gray-500">px</span>
                                    </p>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-500">Tamaño del archivo</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">
                                        <span x-text="imagen.peso ?? 'Calculando...'"></span>
                                        <span x-show="imagen.peso" class="text-xs text-gray-500">KB</span>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Edición del nombre --}}
                    <form :action="urlActualizar" method="POST" class="mt-6">
                        @csrf
                        @method('PUT')

                        <label for="nombre_imagen" class="mb-2 block text-sm font-semibold text-gray-700">
                            Editar nombre
                        </label>

                        <input id="nombre_imagen" type="text" name="nombre" x-model="nombre" maxlength="50" required
                            class="w-full rounded-lg border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

                        @error('nombre')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Guardado y cancelación de edición --}}
                        <div class="mt-5 flex flex-wrap justify-end gap-3">
                            <button type="button" @click="abierto = false"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Guardar
                            </button>
                        </div>
                    </form>

                    {{-- Eliminación de imagen --}}
                    <div class="mt-8 border-t border-gray-200 pt-5 md:mt-auto md:pt-6">
                        <form :action="urlEliminar" method="POST"
                            onsubmit="return confirm('¿Deseas eliminar esta imagen? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="w-full rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Eliminar imagen
                            </button>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
</template>