<x-brilliant-portal-framework::heading
    element="h6"
    {{ $attributes->merge([
        'textSize' => 'text-base',
        'margin' => 'mt-4',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
