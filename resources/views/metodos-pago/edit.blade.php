<x-app-layout>
    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Editar Método de Pago
            </h2>

            <form action="{{ route('metodos-pago.update', $metodoPago) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Método de Pago
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre', $metodoPago->nombre) }}"
                        maxlength="50"
                        required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    >

                    @error('nombre')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                        <span class="text-gray-400 font-normal">(Opcional)</span>
                    </label>

                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="3"
                        maxlength="255"
                        placeholder="Agrega una descripción del método de pago..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    >{{ old('descripcion', $metodoPago->descripcion) }}</textarea>

                    @error('descripcion')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a
                        href="{{ route('metodos-pago.index') }}"
                        wire:navigate
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm"
                    >
                        Actualizar Método de Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
