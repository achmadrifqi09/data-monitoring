<button
    {{
        $attributes->merge([
            'type' => 'button',
            'class' => 'mb-2 me-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-red-800'
        ])
    }}
>
    {{ $slot }}
</button>
