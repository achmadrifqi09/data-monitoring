<div>
    <div class="mb-4 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">User dan Akses</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Kelola user dan akses user</p>
        </div>
    </div>
    <div
        class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="me-2">
                <a href="/control/user"
                    class="{{ Route::is('user.manage') ? 'text-red-600 border-red-600 dark:text-white dark:border-white' : 'text-gray-500 dark:text-gray-400 border-transparent' }} inline-block p-4 border-b-2 rounded-t-lg hover:text-red-600 hover:border-red-600 hover:dark:border-white dark:hover:text-white">
                    User
                </a>
            </li>
            <li class="me-2">
                <a href="/control/access"
                    class="{{ Route::is('user.access') ? 'text-red-600 border-red-600 dark:text-white dark:border-white' : 'text-gray-500 dark:text-gray-400 border-transparent' }} inline-block p-4 border-b-2 rounded-t-lg hover:text-red-600 hover:border-red-600 hover:dark:border-white dark:hover:text-white">
                    Akses Sistem
                </a>
            </li>
        </ul>
    </div>
</div>
