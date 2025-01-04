@extends('layouts.base')

@section('title', 'Login')

@section('content')
    <main class="flex h-dvh w-full items-center justify-center px-4 md:px-0">
        <div
            class="h-auto w-full max-w-[32em] rounded-lg border p-6 shadow dark:border-gray-700 dark:bg-mirage dark:text-white dark:shadow-md"
        >
            <div class="mb-8 flex  md:justify-between items-center flex-col-reverse md:flex-row  gap-4">
                <div>
                    <h3 class="md:text-start text-center text-2xl font-semibold">Login</h3>
                    <p class="md:text-start text-center text-sm text-gray-500 dark:text-gray-400">Monitoring data kontrak proc</p>
                </div>
                <img alt="logo" src="{{ asset('/images/full-logo.png') }}" class="h-14 w-auto" />
            </div>
            <form action="/login" method="post">
                @csrf
                <x-input-label
                    type="text"
                    label="Username"
                    id="username"
                    placeholder="Masukkan username"
                    name="username"
                />

                <x-input-label
                    label="Password"
                    id="password"
                    autocomplete="off"
                    placeholder="Masukkan password"
                    name="password"
                    type="password"
                />
                <div class="flex items-center">
                    <input
                        id="remember"
                        type="checkbox"
                        value=""
                        name="remember"
                        class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-red-600 focus:ring-2 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-red-600"
                    />
                    <label for="remember" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Simpan sesi login
                    </label>
                </div>
                @error('credential-error')
                    <span class="mt-4 block text-sm text-red-500">{{ $message }}</span>
                @enderror

                <div class="mt-6 flex justify-end">
                    <x-button class="min-w-32" type="submit">Login</x-button>
                </div>
            </form>
        </div>
    </main>
@endsection
