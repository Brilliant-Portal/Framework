<{{ $element }}
{{ $attributes
    ->except([
        'element',
        'text',
        'other',
    ])
    ->class([
        $margin ?? 'mt-4 mb-4',
        'font-semibold',
        $text ?? 'text-3xl',
        'text-gray-800 leading-tight',
        $other ?? '',
    ])
}}>
    {{ $slot }}
</{{ $element }}>
