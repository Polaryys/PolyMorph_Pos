
<x-app-layout>
 <div class="mx-auto max-w-5xl">
        {{-- ENCABEZADO --}}
        <div class="mb-6">
            <h1 class="mt-3 text-2xl font-bold text-gray-900">
                Registrar control nutricional
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Registra las medidas y evolución del infante.
            </p>
        </div>
        {{-- ERRORES --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                <p class="font-semibold text-red-700">
                    Verifica los siguientes datos:
                </p>
                <ul class="mt-2 list-disc pl-5 text-sm text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
         <form method="POST"
              action="{{ route('infante.stores',$infante) }}"
              class="space-y-6">
               @include('control-infantes._form', ['readonly' => false])
            </form>
        </div>
    </div>
    
</x-app-layout>
