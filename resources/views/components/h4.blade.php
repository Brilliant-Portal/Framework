<x-brilliant-portal-framework::heading
    element="h4"
    {{ $attributes->merge([
        'textSize' => 'text-lg',
        'margin' => 'mt-4 mb-2',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
