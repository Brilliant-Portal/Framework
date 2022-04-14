<x-brilliant-portal-framework::button-link
    bg="bg-gray-100 dark:bg-gray-900"
    textColor="text-gray-500"
    hover="hover:bg-gray-200 dark:hover:bg-gray-800"
    focus="focus:border-gray-500 focus:ring-gray-200 focus:bg-gray-200"
    active="active:bg-gray-200 dark:active:bg-gray-700"
    other="cursor-not-allowed"
    {{ $attributes }}
>
    {{ $slot }}
</x-brilliant-portal-framework::button-link>
