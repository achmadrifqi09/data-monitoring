@extends('layouts.base')

@section('title', 'Login')

@section('content')
    <main class="flex h-dvh w-full items-center justify-center px-4 md:px-0">
        <div
            class="h-auto w-full max-w-[32em] rounded-lg border p-6 shadow dark:border-gray-700 dark:bg-mirage dark:text-white dark:shadow-md">
            <div class="mb-8 flex items-center gap-4">
                <img alt="logo" src="{{ asset('/images/logo.png') }}" class="w-14 h-14" />
                <div>
                    <h3 class="text-2xl font-semibold">Login</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monitoring data kontrak proc</p>
                </div>
            </div>
            <form action="/login" method="post">
                @csrf
                <x-input-label type="text" label="Username" id="username" placeholder="Masukkan username"
                    name="username" />

                <x-input-label label="Password" id="password" placeholder="Masukkan password" name="password"
                    type="password" />
                <div class="flex items-center">
                    <input id="remember" type="checkbox" value="" name="remember"
                        class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Simpan sesi login
                    </label>
                </div>
                @error('credential-error')
                    <span class="text-red-500 text-sm block mt-4">{{ $message }}</span>
                @enderror

                <div class="mt-6 flex justify-end">
                    <x-button class="min-w-32" type="submit">Login</x-button>
                </div>
            </form>
        </div>
    </main>
@endsection
