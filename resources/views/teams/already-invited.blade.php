<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <p class="mb-4">{{ __('To join an existing team, click the “Accept Invitation” button in the team invitation email.') }}</p>
        <p>{{ __('Didn’t get a team invitation? Ask a member of an existing team to invite you to their team.') }}</p>
    </x-authentication-card>
</x-guest-layout>
