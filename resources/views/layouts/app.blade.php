<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script>
        if (
            localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link rel="icon" href="{{ asset('/images/logo.png') }}" type="image/x-icon">
    <title>@yield('title') | Monitoring Kontrak Proc</title>
    @notifyCss
    <link rel="stylesheet" href="{{ asset('css/global/select2.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased dark:bg-mirage">
    <x-wrapper>
        @yield('content')
    </x-wrapper>
    <div class="fixed z-50">
        <x-notify::notify />
    </div>
    @notifyJs
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/global/select2.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
