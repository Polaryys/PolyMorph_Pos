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
            motivo: @js(old('motivo', '')),
            cantidadEsValida() {
                const cantidad = Number(this.cantidadFinal);
                return this.producto !== null &&
                    this.cantidadFinal !== '' &&
                    Number.isInteger(cantidad) &&
                    cantidad >= 0 &&
                    cantidad <= 4294967295;
            }
        }" @producto-seleccionado.window="producto = $event.detail.producto; cantidadFinal = ''"
        @producto-deseleccionado.window="producto = null; cantidadFinal = ''; motivo = ''">
        @csrf

        <x-product-search :action="route('inventario.buscar-productos-ajuste')" label="Buscar producto" placeholder="Buscar por nombre o SKU..." />

        <x-table>
            <x-slot:headers>
                <th class="w-1/4 px-4 py-3 text-center">Cantidad</th>
                <th class="w-1/4 px-4 py-3 text-center">Diferencia</th>
                <th class="w-1/4 px-4 py-3 text-center">Inventario final</th>
                <th class="w-1/4 px-4 py-3 text-center">Motivo de ajuste <span class="text-red-500">*</span></th>
            </x-slot:headers>

            <tr x-show="producto !== null" x-cloak class="bg-indigo-50/50">
                <td class="px-4 py-3 text-center tabular-nums text-slate-700" x-text="producto?.cantidad"></td>
                <td class="px-4 py-3 text-center font-semibold tabular-nums"
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
                <td class="px-4 py-3 text-center">
                    <input type="number" name="cantidad" min="0" max="4294967295" step="1" required
                        x-model="cantidadFinal" x-bind:disabled="!producto" placeholder="Cantidad final"
                        aria-label="Inventario final"
                        class="mx-auto block w-full max-w-[9rem] rounded-lg border border-slate-300 px-3 py-2 text-center text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </td>
                <td class="px-4 py-3 text-center">
                    <input id="motivo" type="text" name="motivo" maxlength="500" required
                        x-model="motivo" x-bind:disabled="!producto"
                        aria-label="Motivo de ajuste"
                        class="mx-auto block w-full max-w-[20rem] rounded-lg border border-slate-300 px-3 py-2 text-center text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </td>
            </tr>
        </x-table>

        <p x-show="producto" x-cloak class="text-sm text-slate-500">
            Solo puedes ajustar un producto a la vez. Para elegir otro, usa el buscador.
        </p>

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
