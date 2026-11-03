<x-app-layout>

    @if (session('success'))
        <div id="success-message" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
            {{ session('success') }}
        </div>

        <script>
            setTimeout(() => {
                document.getElementById('success-message')?.remove();
            }, 3000);
        </script>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form id="form-ajuste" action="{{ route('inventario.ajustar') }}" method="POST" class="space-y-3"
        x-data="{
            producto: null,
            cantidadFinal: '',
            cantidadEsValida() {
                const cantidad = Number(this.cantidadFinal);
                return this.producto !== null &&
                    this.cantidadFinal !== '' &&
                    Number.isInteger(cantidad) &&
                    cantidad >= 0 &&
                    cantidad <= 4294967295;
            }
        }" @producto-seleccionado.window="producto = $event.detail.producto; cantidadFinal = ''"
        @producto-deseleccionado.window="producto = null; cantidadFinal = ''">
        @csrf

        <x-product-search :action="route('inventario.buscar-productos-ajuste')" label="Buscar producto" placeholder="Buscar por nombre o SKU..." />

        <x-table>
            <x-slot:headers>
                <th class="px-4 py-3 text-left whitespace-nowrap">Imagen</th>
                <th class="px-4 py-3 text-left whitespace-nowrap">SKU</th>
                <th class="px-4 py-3 text-left whitespace-nowrap">Nombre</th>
                <th class="px-4 py-3 text-right whitespace-nowrap">Cantidad</th>
                <th class="px-4 py-3 text-right whitespace-nowrap">Diferencia</th>
                <th class="px-4 py-3 text-left whitespace-nowrap">Inventario final</th>
                <th class="px-4 py-3 text-left whitespace-nowrap">Acción</th>
            </x-slot:headers>

            <tr x-show="producto !== null" x-cloak class="bg-indigo-50/50">
                <td class="px-4 py-3">
                    <div class="h-12 w-12 overflow-hidden rounded-lg bg-slate-100">
                        <template x-if="producto && producto.imagen">
                            <img :src="producto.imagen" :alt="producto.nombre" class="h-full w-full object-cover">
                        </template>
                        <template x-if="producto && !producto.imagen">
                            <div class="flex h-full w-full items-center justify-center">
                                <x-lucide-image class="h-6 w-6 text-slate-400" />
                            </div>
                        </template>
                    </div>
                </td>
                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-700" x-text="producto?.sku || '—'"></td>
                <td class="min-w-52 px-4 py-3 text-slate-800" x-text="producto?.nombre"></td>
                <td class="px-4 py-3 text-right tabular-nums text-slate-700" x-text="producto?.cantidad"></td>
                <td class="px-4 py-3 text-right font-semibold tabular-nums"
                    :class="{
                        'text-red-600': producto && cantidadEsValida() && Number(cantidadFinal) < Number(producto
                            .cantidad),
                        'text-blue-600': producto && cantidadEsValida() && Number(cantidadFinal) > Number(producto
                            .cantidad),
                        'text-slate-500': !producto || !cantidadEsValida() || Number(cantidadFinal) === Number(producto
                            .cantidad)
                    }"
                    x-text="producto && cantidadEsValida() ? Number(cantidadFinal) - Number(producto.cantidad) : 0">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="cantidad" min="0" max="4294967295" step="1" required
                        x-model="cantidadFinal" x-bind:disabled="!producto" placeholder="Cantidad final"
                        aria-label="Inventario final"
                        class="w-36 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </td>
                <td class="px-4 py-3 text-sm text-slate-500">Producto seleccionado</td>
            </tr>
        </x-table>

        <p x-show="producto" x-cloak class="text-sm text-slate-500">
            Solo puedes ajustar un producto a la vez. Para elegir otro, usa el buscador.
        </p>

        <div>
            <x-input-label for="umbral">
                Motivo del ajuste <span class="text-red-500">*</span>
            </x-input-label>
            <textarea id="motivo" name="motivo" rows="3" maxlength="500" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2 mt-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('motivo') }}</textarea>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            <x-secondary-link href="{{ route('articulos.index') }}">
                Cancelar
            </x-secondary-link>
            <x-primary-button x-bind:disabled="!cantidadEsValida()"
                class="disabled:cursor-not-allowed disabled:opacity-50">
                Guardar ajuste
            </x-primary-button>
        </div>
    </form>
</x-app-layout>
