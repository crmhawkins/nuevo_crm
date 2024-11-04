<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts y estilos -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.svg') }}" type="image/x-icon">

    <!-- CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @if ($isDarkMode)
        @vite(['resources/sass/dark-mode.scss'])
    @else
        @vite(['resources/sass/light-mode.scss'])
    @endif --}}
    @yield('css')
    <link rel="stylesheet" href="{{ asset('build/assets/app-d2e38ed8.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="{{ asset('build/assets/app-bf7e6802.js') }}"></script>
    @laravelViewsStyles
</head>
<body class="fondoPortal">
    <div id="appPortal">

        {{-- <div id="loadingOverlay" style="display: block; position: fixed; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.5); z-index: 50000; cursor: pointer;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-black" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div> --}}
        {{-- <div class="css-96uzu9"></div> --}}

        @include('layouts.sidebarPortal')

        <main id="mainPortal">
            {{-- @include('layouts.topBar') --}}
            <div class="contenedor p-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script> --}}
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    @yield('scriptsSidebar')
    @yield('scripts')

    {{-- <script src="{{ asset('assets/js/main.js') }}"></script> --}}
    @laravelViewsScripts
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var loader = document.getElementById('loadingOverlay');
            if (loader) {
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 2000);
            }
        });


        function saveThemePreference(isDark) {
            $.ajax({
                url: '{{ route("saveThemePreference") }}',
                method: 'POST',
                data: {
                    is_dark: isDark,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response.message);
                    // Cambiar dinámicamente el tema
                    document.getElementById('body').classList.toggle('dark-mode', isDark);
                },
                error: function(error) {
                    console.error('Error guardando la preferencia de tema:', error);
                }
            });
        }

        // Evento para cambiar el tema
        // document.getElementById('toggleThemeButton').addEventListener('click', function() {
        //     var isDark = document.getElementById('body').classList.toggle('dark-mode');
        //     saveThemePreference(isDark);
        // });

        function updateThemeIcon(isDark) {
            const themeIcon = document.getElementById('theme-icon');
            if (isDark) {
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-brightness-high');
            } else {
                themeIcon.classList.remove('bi-brightness-high');
                themeIcon.classList.add('bi-moon');
            }
        }

        // Evento para cambiar el tema
        document.getElementById('light-dark-mode').addEventListener('click', function() {
            const body = document.getElementById('body');
            const isDark = body.classList.toggle('dark-mode');
            saveThemePreference(isDark);
            console.log(isDark)
        });
    </script>
</body>
</html>
