<x-app-layout>


    <div class="space-y-8 divide-y divide-gray-200">

        <!-- perfil y foto -->
        @include('profile.partials.update-profile-information-form')

        <!-- password -->
        <div class="pt-8">
            @include('profile.partials.update-password-form')
        </div>

    </div>

</x-app-layout>