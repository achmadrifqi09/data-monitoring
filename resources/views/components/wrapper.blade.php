<div>
    <x-navbar />
    <x-sidebar />
    <div
        class="p-4 sm:ml-64 min-h-[calc(100dvh_-_56px)] mt-[56px] bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        {{ $slot }}
    </div>
</div>
