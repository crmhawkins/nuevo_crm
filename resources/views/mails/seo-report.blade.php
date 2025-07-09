<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #1a56db;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }

        .pin-box {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .pin-number {
            font-size: 24px;
            font-weight: bold;
            color: #1a56db;
            letter-spacing: 2px;
        }

        .button {
            display: inline-block;
            background-color: #1a56db;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Informe SEO Disponible</h1>
        </div>

        <div class="content">
            <p>Estimado cliente,</p>

            <p>Le informamos que se ha generado un nuevo informe SEO para el dominio
                <strong>{{ $domain }}</strong>.</p>

            <p>Para acceder al informe, necesitará el siguiente PIN:</p>

            <div class="pin-box">
                <p>PIN de acceso:</p>
                <div class="pin-number">{{ $pin }}</div>
            </div>

            <p>Puede ver su informe en el siguiente enlace:</p>

            <center>
                <a href="https://crm.hawkins.es/autoseo/reports" class="button" target="_blank" style="background-color: #1a56db; color: white; text-decoration: none; padding: 12px 25px; border-radius: 5px; display: inline-block;">
                    Ver Informe SEO
                </a>
            </center>

            <p>Recuerde guardar este PIN de forma segura, ya que lo necesitará para acceder a futuros informes.</p>

            <div class="footer">
                <p>Este es un correo automático. Por favor, no responda a este mensaje.</p>
                <p>&copy; {{ date('Y') }} Hawkins Digital - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</body>

</html>
