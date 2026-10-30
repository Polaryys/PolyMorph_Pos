<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-medium text-slate-500">Inventario</p>
            <h1 class="mt-1 text-2xl font-semibold text-slate-900">Ajustar existencias</h1>
            <p class="mt-2 text-sm text-slate-600">
                Busca un producto por nombre o SKU, selecciónalo y registra su cantidad física final.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <label for="buscar-producto" class="mb-1 block text-sm font-medium text-slate-700">
                Buscar producto
            </label>
            <input id="buscar-producto" type="search" maxlength="100" autocomplete="off"
                placeholder="Buscar por nombre o SKU"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
            <p id="error-busqueda" class="mt-2 hidden text-sm text-red-600" role="alert">
                No se pudo completar la búsqueda. Intenta nuevamente.
            </p>
        </div>

        <form id="form-ajuste" action="{{ route('inventario.ajustar') }}" method="POST"
            class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <input id="inventario_id" name="inventario_id" type="hidden" value="">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-4 py-3">Imagen</th>
                            <th scope="col" class="px-4 py-3">SKU</th>
                            <th scope="col" class="px-4 py-3">Nombre</th>
                            <th scope="col" class="px-4 py-3 text-right">Cantidad</th>
                            <th scope="col" class="px-4 py-3 text-right">Diferencia</th>
                            <th scope="col" class="px-4 py-3">Inventario final</th>
                            <th scope="col" class="px-4 py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="resultados-productos" class="divide-y divide-slate-100">
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                Escribe un nombre o SKU para buscar productos.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p id="ayuda-seleccion" class="hidden text-sm text-slate-500">
                Solo puedes ajustar un producto a la vez. Para elegir otro, usa el buscador.
            </p>

            <div>
                <label for="motivo" class="mb-1 block text-sm font-medium text-slate-700">Motivo del ajuste</label>
                <textarea id="motivo" name="motivo" rows="3" maxlength="500" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('motivo') }}</textarea>
            </div>

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('articulos.index') }}"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Cancelar
                </a>
                <button id="guardar-ajuste" type="submit" disabled
                    class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Guardar ajuste
                </button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const searchInput = document.getElementById('buscar-producto');
            const results = document.getElementById('resultados-productos');
            const searchError = document.getElementById('error-busqueda');
            const inventoryId = document.getElementById('inventario_id');
            const submitButton = document.getElementById('guardar-ajuste');
            const selectionHelp = document.getElementById('ayuda-seleccion');
            let searchTimeout;
            let searchVersion = 0;

            const showMessage = (message) => {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 7;
                cell.className = 'px-4 py-8 text-center text-slate-500';
                cell.textContent = message;
                row.append(cell);
                results.replaceChildren(row);
            };

            const createImage = (product) => {
                if (!product.imagen_url) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'h-12 w-12 rounded-lg bg-slate-100';
                    placeholder.setAttribute('aria-hidden', 'true');
                    return placeholder;
                }

                const image = document.createElement('img');
                image.src = product.imagen_url;
                image.alt = '';
                image.className = 'h-12 w-12 rounded-lg object-cover';
                return image;
            };

            const renderProducts = (products) => {
                results.replaceChildren();
                if (products.length === 0) {
                    showMessage('No se encontraron productos.');
                    return;
                }

                products.forEach((product) => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-slate-50';

                    const imageCell = document.createElement('td');
                    imageCell.className = 'px-4 py-3';
                    imageCell.append(createImage(product));

                    const skuCell = document.createElement('td');
                    skuCell.className = 'whitespace-nowrap px-4 py-3 font-medium text-slate-700';
                    skuCell.textContent = product.sku || '—';

                    const nameCell = document.createElement('td');
                    nameCell.className = 'min-w-52 px-4 py-3 text-slate-800';
                    nameCell.textContent = product.nombre;

                    const quantityCell = document.createElement('td');
                    quantityCell.className = 'px-4 py-3 text-right tabular-nums text-slate-700';
                    quantityCell.textContent = product.cantidad;

                    const differenceCell = document.createElement('td');
                    differenceCell.className = 'px-4 py-3 text-right tabular-nums text-slate-500';
                    differenceCell.textContent = '0';

                    const finalCell = document.createElement('td');
                    finalCell.className = 'px-4 py-3 text-slate-400';
                    finalCell.textContent = '—';

                    const actionCell = document.createElement('td');
                    actionCell.className = 'px-4 py-3';
                    const selectButton = document.createElement('button');
                    selectButton.type = 'button';
                    selectButton.className = 'whitespace-nowrap rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100';
                    selectButton.textContent = 'Seleccionar';
                    selectButton.addEventListener('click', () => selectProduct(product));
                    actionCell.append(selectButton);

                    row.append(imageCell, skuCell, nameCell, quantityCell, differenceCell, finalCell, actionCell);
                    results.append(row);
                });
            };

            const selectProduct = (product) => {
                clearTimeout(searchTimeout);
                searchVersion++;
                inventoryId.value = product.inventario_id;
                selectionHelp.classList.remove('hidden');

                const row = document.createElement('tr');
                row.className = 'bg-indigo-50/50';

                const imageCell = document.createElement('td');
                imageCell.className = 'px-4 py-3';
                imageCell.append(createImage(product));

                const skuCell = document.createElement('td');
                skuCell.className = 'whitespace-nowrap px-4 py-3 font-medium text-slate-700';
                skuCell.textContent = product.sku || '—';

                const nameCell = document.createElement('td');
                nameCell.className = 'min-w-52 px-4 py-3 text-slate-800';
                nameCell.textContent = product.nombre;

                const quantityCell = document.createElement('td');
                quantityCell.className = 'px-4 py-3 text-right tabular-nums text-slate-700';
                quantityCell.textContent = product.cantidad;

                const differenceCell = document.createElement('td');
                differenceCell.className = 'px-4 py-3 text-right font-semibold tabular-nums text-slate-500';
                differenceCell.textContent = '0';

                const finalCell = document.createElement('td');
                finalCell.className = 'px-4 py-3';
                const finalInput = document.createElement('input');
                finalInput.type = 'number';
                finalInput.name = 'cantidad';
                finalInput.min = '0';
                finalInput.max = '4294967295';
                finalInput.step = '1';
                finalInput.required = true;
                finalInput.placeholder = 'Cantidad final';
                finalInput.setAttribute('aria-label', 'Inventario final');
                finalInput.className = 'w-36 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
                finalCell.append(finalInput);

                const actionCell = document.createElement('td');
                actionCell.className = 'px-4 py-3 text-sm text-slate-500';
                actionCell.textContent = 'Producto seleccionado';

                row.append(imageCell, skuCell, nameCell, quantityCell, differenceCell, finalCell, actionCell);
                results.replaceChildren(row);

                finalInput.addEventListener('input', () => {
                    const hasValue = finalInput.value !== '';
                    const finalQuantity = Number(finalInput.value);
                    const valid = hasValue
                        && Number.isInteger(finalQuantity)
                        && finalQuantity >= 0
                        && finalQuantity <= 4294967295;

                    differenceCell.textContent = valid
                        ? String(finalQuantity - Number(product.cantidad))
                        : '0';
                    differenceCell.classList.toggle('text-red-600', valid && finalQuantity < product.cantidad);
                    differenceCell.classList.toggle('text-blue-600', valid && finalQuantity > product.cantidad);
                    differenceCell.classList.toggle('text-slate-500', !valid || finalQuantity === product.cantidad);
                    submitButton.disabled = !valid;
                });

                searchError.classList.add('hidden');
            };

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                const version = ++searchVersion;
                inventoryId.value = '';
                selectionHelp.classList.add('hidden');
                submitButton.disabled = true;
                searchError.classList.add('hidden');

                const search = searchInput.value.trim();
                if (!search) {
                    showMessage('Escribe un nombre o SKU para buscar productos.');
                    return;
                }

                showMessage('Buscando productos…');
                searchTimeout = setTimeout(async () => {
                    try {
                        const url = new URL(@json(route('inventario.buscar-productos-ajuste')), window.location.origin);
                        url.searchParams.set('buscar', search);
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!response.ok) {
                            throw new Error('Search request failed.');
                        }

                        const products = await response.json();
                        if (version === searchVersion) {
                            renderProducts(products);
                        }
                    } catch {
                        if (version === searchVersion) {
                            showMessage('Ocurrió un error al buscar productos.');
                            searchError.classList.remove('hidden');
                        }
                    }
                }, 300);
            });
        })();
    </script>
</x-app-layout>
