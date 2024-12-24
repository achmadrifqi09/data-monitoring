<div class="w-max h-max fixed md:bottom-8 md:right-8 bottom-4 right-4">
    <div
        id="toast"
        class="flex w-full max-w-xs items-center space-x-4 divide-x divide-gray-200 rounded-lg bg-white p-4 text-gray-500 shadow dark:divide-gray-700 border dark:border-gray-700 dark:bg-clay dark:text-gray-400 rtl:space-x-reverse rtl:divide-x-reverse hidden"
        role="alert"
    >
        @if($type === 'success')
            <x-solar-verified-check-linear class="w-6 h-6 text-green-500"/>
        @endif
        @if($type === 'error')
            <x-solar-close-circle-linear class="w-6 h-6 text-green-500"/>
        @endif
        <div class="ps-4 text-sm font-normal">{{$message}}</div>
    </div>
</div>
