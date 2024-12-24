<nav class="fixed top-0 z-50 w-full border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-clay">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button
                    data-drawer-target="logo-sidebar"
                    data-drawer-toggle="logo-sidebar"
                    aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 sm:hidden"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg
                        class="h-6 w-6"
                        aria-hidden="true"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            clip-rule="evenodd"
                            fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                        ></path>
                    </svg>
                </button>
                <a href="/" class="gap-2 items-center ms-2 flex md:me-24">
                    <img alt="logo" src="{{asset('/images/logo.png')}}" class="w-8 h-8"/>
                    <div class="relative">
                        <span class="block text-xs text-mirage dark:text-white relative -bottom-1">Monitoring data</span>
                        <span class="block font-semibold text-mirage dark:text-white sm:text-lg m-0">Kontrak Proc</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center">
                <button
                    type="button"
                    id="darkModeButton"
                    class="me-2 rounded-lg border border-gray-300 bg-white p-2 text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                >
                    <span id="darkIcon">
                        <x-solar-moon-stars-bold class="hidden h-5 w-5 text-gray-500 dark:block dark:text-white" />
                    </span>
                    <span id="lightIcon">
                        <x-solar-sun-bold class="block h-5 w-5 text-gray-500 dark:hidden dark:text-white" />
                    </span>
                </button>
                <div class="relative ms-3 flex items-center">
                    <div>
                        <button
                            type="button"
                            class="flex rounded-full text-sm focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-expanded="false"
                            data-dropdown-toggle="dropdown-user"
                        >
                            <span class="sr-only">Open user menu</span>
                            <span
                                class="flex aspect-square h-10 w-10 items-center justify-center rounded-full bg-gray-100 p-2 font-semibold dark:bg-gray-700 dark:text-white"
                            >
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        </button>
                    </div>
                    <div
                        class="z-50 my-4 hidden min-w-40 list-none divide-y divide-gray-100 rounded bg-white text-base shadow dark:divide-gray-600 dark:bg-gray-700"
                        id="dropdown-user"
                    >
                        <ul class="py-1" role="none">
                            <li>
                                <a
                                    href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                    role="menuitem"
                                >
                                    Settings
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                        role="menuitem"
                                    >
                                        Log out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
