<x-brilliant-portal-framework::heading
    element="h5"
    {{ $attributes->merge([
        'textSize' => 'text-base',
        'margin' => 'mt-4',
    ]) }}
>
    {{ $slot }}
</x-brilliant-portal-framework::heading>
