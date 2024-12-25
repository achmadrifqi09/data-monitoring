@php
    use Illuminate\Support\Str;
@endphp

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-clay dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-clay">
        <ul class="space-y-2 font-normal">
            @foreach ($menus as $menu)
                <li>
                    <a href="{{ $menu['path'] }}"
                        class="flex items-center p-2 rounded-lg
                        {{ str_contains(Route::currentRouteName(), $menu['routeName']) ? 'text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white' : 'hover:dark:text-white hover:text-gray-500 dark:text-gray-500 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <i class="{{ $menu['icon'] }}"></i>
                        <span class="ms-3">{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
            <li>
                <button type="button"
                    class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:dark:text-white hover:text-gray-500 dark:text-gray-500 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                    <i class="fa-regular fa-window-maximize"></i>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Area Kontrol</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <ul id="dropdown-example" class="hidden py-2 space-y-2">
                    <li>
                        <a href="#"
                            class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                            User
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                            Proyek
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
    </div>
</aside>
