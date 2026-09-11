<nav x-data="{ open: false }"
    class="fixed top-12 left-0 right-0 md:left-56 md:right-0 z-30 bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('ficha-infantes.index')" :active="request()->routeIs('ficha-infantes.index')">
                        {{ __('Ficha Infantes') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('control-infantes.index')" :active="request()->routeIs('control-infantes.index')">
                        {{ __('Control Infantes') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </div>


</nav>