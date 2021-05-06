<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('brilliant-portal-framework.teams.store-first') }}">
            @csrf

            <div>
                <x-jet-label for="name" value="{{ __('Team Name') }}" />
                <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('brilliant-portal-framework.teams.already-invited') }}">
                    {{ __('Invited to join an existing team?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Create Team') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
