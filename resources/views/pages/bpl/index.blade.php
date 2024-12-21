@extends('layouts.app')

@section('title', 'BPL')

@section('content')
    <div class="flex justify-between gap-6 items-start flex-col md:flex-row md:items-center mb-6">
        <div>
            <h4 class="text-xl font-medium">BPL</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data BPL</p>
        </div>
        <div class="inline-flex rounded-sm mb-6 h-max" role="group">
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-mirate bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-clay dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                Tambah
            </button>
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-mirate bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-clay dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                Export
            </button>
            <button type="button"
                class="px-4 py-2 text-sm font-medium text-mirate bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-clay dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                Import
            </button>
        </div>
    </div>

    <div class="relative overflow-x-auto sm:rounded-md">
        <table class="w-full text-sm text-left rtl:text-right text-mirage dark:text-white">
            <thead class="text-xs text-mirage uppercase bg-gray-100 dark:bg-gray-700 dark:text-white">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Product name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Color
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Price
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i < 4; $i++)
                    <tr class="bg-white border-b dark:bg-clay dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-clay/80">
                        <td class="px-6 py-4">
                            Apple MacBook Pro 17"
                        </td>
                        <td class="px-6 py-4">
                            Silver
                        </td>
                        <td class="px-6 py-4">
                            Laptop
                        </td>
                        <td class="px-6 py-4">
                            $2999
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @endfor

            </tbody>
        </table>
    </div>
@endsection
