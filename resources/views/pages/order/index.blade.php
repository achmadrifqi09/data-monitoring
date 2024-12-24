@extends('layouts.app')

@section('title', 'Order')

@section('content')
    <div class="mb-8 mt-4 flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div>
            <h4 class="text-xl font-medium">BPL</h4>
            <p class="text-sm dark:text-gray-300 xl:text-base">Pengelolaan data BPL</p>
        </div>
        <div class="inline-flex h-max rounded-sm" role="group">
            <a
                href="/order/form"
                class="rounded-s-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Tambah
            </a>

            <button
                type="button"
                class="border-b border-t border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Export
            </button>
            <button
                type="button"
                class="rounded-e-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:text-red-700 focus:ring-2 focus:ring-red-700 dark:border-gray-700 dark:bg-clay dark:text-white dark:hover:bg-gray-700 dark:hover:text-white dark:focus:text-white dark:focus:ring-red-500"
            >
                Import
            </button>
        </div>
    </div>

    <div>
        <form class="mb-4 flex max-w-sm items-center" action="{{ route('order.view') }}" method="get">
            <label for="simple-search" class="sr-only">Search</label>
            <div class="relative w-full">
                <input
                    value="{{ request('search') }}"
                    type="text"
                    id="search"
                    name="search"
                    class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-red-500 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-red-500 dark:focus:ring-red-500"
                    placeholder="Cari data bpl ..."
                />
                @if (request('search'))
                    <a
                        href="{{ route('order.view') }}"
                        class="absolute right-0.5 top-0.5 z-10 rounded-md bg-gray-50 p-2 dark:bg-gray-700"
                    >
                        <x-solar-close-circle-linear class="h-5 w-5 text-gray-400" />
                        <span class="sr-only">Clear</span>
                    </a>
                @endif
            </div>
            <button
                type="submit"
                class="ms-3 rounded-lg border border-gray-300 bg-white p-[10px] text-sm text-mirage hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-clay dark:text-gray-100 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
            >
                <x-solar-magnifer-linear class="h-5 w-5" />
                <span class="sr-only">Search</span>
            </button>
        </form>
    </div>
@endsection

@section('scripts')

@endsection
