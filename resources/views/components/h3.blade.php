<x-brilliant-portal-framework::heading
    element="h3"
    {{ $attributes->merge([
        'textSize' => 'text-xl',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
