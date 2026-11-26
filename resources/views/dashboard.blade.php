<x-app-layout>
    <div class="flex min-h-[calc(100vh-8rem)] flex-col">

        <!-- Contenido principal -->
        <div class="flex flex-1 flex-col items-center pt-4">
            <div class="w-full px-2 sm:px-4">

                <div class="space-y-8">

                    <!-- Presentación -->
                    <div class="flex flex-col items-center gap-4">

                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-app-primary-200 bg-app-primary-100/50 px-3 py-1.5 text-sm font-medium text-app-primary-700 dark:border-app-primary-800 dark:bg-app-primary-900/30 dark:text-app-primary-300">
                            <span class="size-2 rounded-full bg-app-primary-500"></span>
                            Sistema de gestión comercial
                        </span>

                        <!-- Logo y título -->
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <x-icon-sistema
                                class="size-24 shrink-0 fill-current text-app-primary-600 dark:text-app-primary-400" />

                            <h1
                                class="text-center text-4xl font-extrabold tracking-tight text-app-text-primary sm:text-5xl">
                                MultiServicios
                                <span class="text-app-primary-600 dark:text-app-primary-400">
                                    Eben-Ezer
                                </span>
                            </h1>
                        </div>

                        <!-- Descripción -->
                        <p class="max-w-3xl text-center text-base leading-7 text-app-text-secondary sm:text-lg">
                            Una solución informática diseñada para centralizar y facilitar
                            la administración del negocio, integrando sus principales
                            operaciones en un entorno organizado, práctico y confiable.
                        </p>
                    </div>

                    <!-- Módulos -->
                    <div class="space-y-5">

                        <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-5">

                            <div class="flex items-center gap-3">
                                <x-lucide-shopping-cart
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Ventas</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-package
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Inventario</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-users
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Clientes</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-truck
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Proveedores</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-shopping-bag
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Compras</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-chart-no-axes-combined
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Reportes</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-lucide-images
                                    class="size-5 shrink-0 text-app-primary-600 dark:text-app-primary-400" />
                                <span class="text-sm font-medium text-app-text-primary">Medios audiovisuales</span>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <footer class="border-t border-app-border py-3">
            <div class="flex flex-col items-center justify-between gap-2 text-center sm:flex-row">
                <p class="text-xs text-app-text-muted">
                    © {{ date('Y') }} Polaryss. Todos los derechos reservados.
                </p>

                <p class="text-xs font-semibold tracking-wide text-app-text-secondary">
                    POLARYSS
                </p>
            </div>
        </footer>

    </div>
</x-app-layout>
