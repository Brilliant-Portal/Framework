<a {{ $attributes->class([
        'inline-flex',
        'items-center',
        $margin ?? 'mb-1 mr-1',
        $padding ?? 'px-4 py-2',
        $bg ?? 'bg-gray-800 dark:bg-gray-100',
        'border',
        'border-transparent',
        'rounded-md',
        'font-semibold',
        $textSize ?? 'text-xs',
        $textColor ?? 'text-white dark:text-gray-700',
        'uppercase',
        'tracking-widest',
        'no-underline',
        $hover ?? 'hover:bg-gray-700 dark:hover:bg-gray-300',
        $active ?? 'active:bg-gray-900 dark:active:bg-gray-500',
        $focus ?? 'focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300',
        'disabled:opacity-25',
        'transition',
        $other ?? 'cursor-pointer',
    ])
    ->except([
        'bg',
        'padding',
        'margin',
        'textSize',
        'textColor',
        'hover',
        'active',
        'focus',
        'other',
    ])
    ->merge([
        'target' => '_self',
    ]) }}
>
    {{ $slot }}
</a>
