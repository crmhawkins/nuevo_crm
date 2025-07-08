<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Nuevo Informe SEO Generado</h1>
        </div>

        <div class="content">
            <p>Hola,</p>

            <p>Se ha generado un nuevo informe SEO para el dominio <strong>{{ $domain }}</strong>.</p>

            <p>Puedes acceder al informe a través del siguiente enlace, con este codigo de acceso:
                <strong>{{ $pin }}</strong>.</p>

            <p>
                <a href="https://crm.hawkins.es/autoseo/reports/" class="button">
                    Ver Informe
                </a>
            </p>

            <p>Saludos,<br>Equipo Hawkins</p>
        </div>
    </div>
</body>

</html>
