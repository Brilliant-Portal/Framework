<a {{ $attributes->class([
        'inline-flex',
        'items-center',
        $margin ?? 'mb-1',
        $padding ?? 'px-4 py-2',
        $bg ?? 'bg-gray-800',
        'border',
        'border-transparent',
        'rounded-md',
        'font-semibold',
        $textSize ?? 'text-xs',
        $textColor ?? 'text-white',
        'uppercase',
        'tracking-widest',
        'no-underline',
        $hover ?? 'hover:bg-gray-700',
        $active ?? 'active:bg-gray-900',
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
