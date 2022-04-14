<{{ $element }}
{{ $attributes
    ->except([
        'element',
        'margin',
        'padding',
        'textSize',
        'textColor',
        'other',
    ])
    ->class([
        $margin ?? 'mt-4 mb-4',
        $padding ?? '',
        'font-semibold',
        $textSize ?? 'text-3xl',
        $textColor ?? 'text-gray-800 dark:text-gray-200',
        'leading-tight',
        $other ?? '',
    ])
}}>
    {{ $slot }}
</{{ $element }}>
