<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('brilliant-portal-framework.teams.store-first') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Team Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('brilliant-portal-framework.teams.already-invited') }}">
                    {{ __('Invited to join an existing team?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Create Team') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
