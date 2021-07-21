<x-brilliant-portal-framework::heading
    element="h1"
    {{ $attributes->merge([
        'textSize' => 'text-3xl',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
