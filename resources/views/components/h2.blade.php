<x-brilliant-portal-framework::heading
    element="h2"
    {{ $attributes->merge([
        'textSize' => 'text-2xl',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
