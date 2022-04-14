<span {{ $attributes
    ->except([
        'bg',
        'text',
        'focus',
        'border',
    ])
    ->class([
        'mt-4 mr-2',
        'whitespace-no-wrap',
        'px-2',
        'py-1',
        'rounded-full',
        'text-xs',
        'font-bold',
        $bg ?? 'bg-gray-400 dark:bg-gray-700',
        $text ?? 'text-white dark:text-gray-900',
        $focus ?? '',
        $border ?? '',
        'uppercase'
    ])
}}>
    {{ $slot }}
</span>
