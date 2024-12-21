<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-clay dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-clay">
        <ul class="space-y-2 font-medium">
            @foreach ($menus as $menu)       
                <li>
                    <a href="{{$menu['path']}}"
                        class="flex items-center p-2 rounded-lg  hover:bg-gray-100 
                        dark:hover:bg-gray-700 group dark:hover:text-white {{Route::is($menu['routeName']) ? 'dark:text-white text-mirage' : 'dark:text-gray-400 text-gray-500'}}">
                        <x-solar-widget-4-bold class="w-5 h-5" />
                        <span class="ms-3">{{$menu['label']}}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
