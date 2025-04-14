<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header con Logo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        header {
            padding: 20px 0;
        }

        .logo-img {
            width: 100%;
            max-width: 15rem; /* Tamaño máximo del logo */
            height: auto;
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex justify-content-center align-items-center">
            <img src="{{ asset('/assets/images/logo/logo.png') }}" alt="Logo" class="logo-img">
        </div>
    </header>

</body>
</html>
