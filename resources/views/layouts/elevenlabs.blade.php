<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', 'ElevenLabs') - {{ config('app.name', 'CRM') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">

    <style>
        :root {
            color-scheme: only light;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f5f9;
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header.elevenlabs-header {
            background: #0f172a;
            color: white;
            padding: 18px 0;
            box-shadow: 0 10px 30px -25px rgba(15, 23, 42, 0.8);
        }
        .elevenlabs-header .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .elevenlabs-header .logo i {
            font-size: 24px;
        }
        .elevenlabs-header nav {
            display: flex;
            gap: 18px;
            align-items: center;
        }
        .elevenlabs-header nav a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-weight: 500;
        }
        .elevenlabs-header nav a.active,
        .elevenlabs-header nav a:hover {
            color: white;
        }
        main.elevenlabs-main {
            flex: 1;
            padding: 32px 0 40px;
        }
        .container-fluid.elevenlabs-container {
            max-width: 1280px;
        }
    </style>

    @yield('css')
</head>
<body>
<header class="elevenlabs-header">
    <div class="container-fluid elevenlabs-container d-flex justify-content-between align-items-center">
        <div class="logo">
            <i class="fa-solid fa-robot"></i>
            <div>
                <strong>ElevenLabs</strong>
                <div class="small text-white-50">Gestión de campañas</div>
            </div>
        </div>
        <nav>
            <a href="{{ route('elevenlabs.gestor.dashboard') }}" class="{{ request()->routeIs('elevenlabs.gestor.*') ? 'active' : '' }}">Campañas</a>
        </nav>
    </div>
</header>

<main class="elevenlabs-main">
    <div class="container-fluid elevenlabs-container">
        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@yield('js')
</body>
</html>
