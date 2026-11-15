<x-app-layout>

    <div x-data="ventasCarrito(@js($clienteSeleccionado), @js($detalles))" x-init="init()"
        class="space-y-3">

        <form id="formulario-venta" action="{{ route('ventas.store') }}" method="POST" class="space-y-5"
            x-ref="formularioVenta" @submit.prevent="confirmarVenta($event)">
            @csrf
            <input type="hidden" name="idempotency_key"
                value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">

            <input type="hidden" name="cliente_id" :value="clienteSeleccionado?.id ?? ''">

            @if (session('success'))
                <x-alert-message />
            @endif

            {{-- Errores de validación --}}
            @if ($errors->any())
                <div id="alert-validation-errors" role="alert"
                    class="mb-4 flex items-center justify-between gap-4 rounded-lg border border-red-200 bg-red-100 p-4 text-sm text-red-700">
                    <div>
                        <p class="font-semibold">No se pudo registrar la venta:</p>

                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <button type="button" onclick="this.parentElement.remove()"
                        class="shrink-0 opacity-70 transition-opacity hover:opacity-100" aria-label="Cerrar alerta">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </div>
            @endif

            {{-- Errores de Alpine --}}
            <p id="alert-alpine-error" x-cloak x-show="errorFormulario"
                class="mb-4 flex items-center justify-between gap-4 rounded-lg border border-red-200 bg-red-100 p-4 text-sm text-red-700"
                role="alert">
                <span x-text="errorFormulario"></span>

                <button type="button" @click="errorFormulario = ''"
                    class="shrink-0 opacity-70 transition-opacity hover:opacity-100" aria-label="Cerrar alerta">
                    <x-lucide-x class="h-5 w-5" />
                </button>
            </p>

            {{-- Error específico de detalles --}}
            @error('detalles')
                <div id="alert-detalles-error" role="alert"
                    class="mb-4 flex items-center justify-between gap-4 rounded-lg border border-red-200 bg-red-100 p-4 text-sm text-red-700">
                    <span>{{ $message }}</span>

                    <button type="button" onclick="this.parentElement.remove()"
                        class="shrink-0 opacity-70 transition-opacity hover:opacity-100" aria-label="Cerrar alerta">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </div>
            @enderror

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @include('ventas.partials._buscador-producto')
                @include('ventas.partials._buscador-cliente')
            </div>
            @include('ventas.partials._detalles')
            @include('ventas.partials._modal-pago')

        </form>
    </div>
</x-app-layout>