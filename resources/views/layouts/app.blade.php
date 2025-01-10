<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title') | Monitoring Kontrak Proc</title>
    @notifyCss
    <link rel="stylesheet" href="{{ asset('css/global/select2.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</head>

<body class="antialiased dark:bg-mirage">
    <x-template>
        @yield('content')
    </x-template>
    <div class="fixed z-50">
        <x-notify::notify />
    </div>
    @notifyJs
    <script src="{{ asset('js/global/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/global/select2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.23/moment-timezone-with-data-2012-2022.min.js">
    </script>
    @stack('scripts')
</body>

</html>
