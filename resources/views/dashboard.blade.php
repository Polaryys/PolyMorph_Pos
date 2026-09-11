<x-app-layout>
    <div>
        <!-- Contenedor de dos columnas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            
            <!-- Columna Izquierda: Texto y Mensaje -->
            <div class="space-y-4">

                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    SOFTWARE AICASA
                </h1>
                <p class="text-base text-gray-600">
                    Descripcion de software.....
                </p>
            </div>

            <!-- Columna Derecha: Imagen -->
            <div class="flex justify-center">
                <img src="https://images.pexels.com/photos/7420514/pexels-photo-7420514.jpeg" 
                        alt="Ilustración del Dashboard" 
                        class="rounded-lg shadow-md w-full max-w-md h-auto object-cover">
            </div>
        </div>
    </div>
</x-app-layout>
