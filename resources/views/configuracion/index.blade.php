
<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                Personalización del sistema
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Configura la apariencia visual de tu sistema.
            </p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="max-w-sm">
                <x-input-label for="accent" value="Color principal" />

                <select
                    id="accent"
                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm
                           focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="indigo">Índigo</option>
                    <option value="blue">Azul</option>
                    <option value="green">Verde</option>
                    <option value="violet">Violeta</option>
                    <option value="rose">Rosa</option>
                </select>

                <p class="mt-2 text-xs text-gray-500">
                    Selecciona el color que deseas utilizar en la interfaz.
                </p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selector = document.getElementById('accent');

            const colores = ['indigo', 'blue', 'green', 'violet', 'rose'];

            const guardado = localStorage.getItem('accent');
            const inicial = colores.includes(guardado) ? guardado : 'indigo';

            document.documentElement.dataset.accent = inicial;
            selector.value = inicial;

            selector.addEventListener('change', () => {
                document.documentElement.dataset.accent = selector.value;
                localStorage.setItem('accent', selector.value);
            });
        });
    </script>
</x-app-layout>
