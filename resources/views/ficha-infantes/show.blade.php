<x-app-layout>
    <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
    
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Ver Ficha de Infante</h2>
            
            <form action="{{ route('ficha-infantes.store') }}" method="POST">
                @include('ficha-infantes._form', ['readonly' => true])
            </form>

        </div>
    </div>
</x-app-layout>


