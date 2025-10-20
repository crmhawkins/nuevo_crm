<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe SEO - Hawkins</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2V6h4V4H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-text {
            font-size: 1.1rem;
            color: #4b5563;
            margin-bottom: 30px;
        }

        .domain-highlight {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-left: 4px solid #6366f1;
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
        }

        .domain-highlight strong {
            color: #4338ca;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .pin-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
            position: relative;
        }

        .pin-section::before {
            content: '🔐';
            font-size: 2rem;
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
        }

        .pin-label {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .pin-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4338ca;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(67, 56, 202, 0.1);
            margin: 0;
        }

        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 8px 16px rgba(67, 56, 202, 0.3);
            transition: all 0.3s ease;
            border: none;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(67, 56, 202, 0.4);
        }

        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            margin: 5px 0;
        }

        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            .email-container {
                padding: 10px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .pin-number {
                font-size: 2rem;
                letter-spacing: 2px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-card">
            <div class="header">
                <div class="header-content">
                    <img src="https://hawkins.es/wp-content/uploads/2022/05/logo-hawkins.png" alt="Hawkins Logo" class="logo">
                    <h1>📊 Nuevo Informe SEO</h1>
                    <p>Su análisis está listo para revisar</p>
                </div>
            </div>

            <div class="content">
                <div class="welcome-text">
                    <p>Hola,</p>
                    <p>Nos complace informarte que hemos generado un nuevo informe SEO para tu sitio web.</p>
                </div>

                <div class="domain-highlight">
                    <p>🌐 <strong>{{ $domain }}</strong></p>
                </div>

                <p>Puedes acceder a tu informe completo a través del siguiente enlace, utilizando este código de acceso:</p>

                <div class="pin-section">
                    <div class="pin-label">Código de Acceso</div>
                    <div class="pin-number">{{ $pin }}</div>
                </div>

                <div class="button-container">
                    <a href="https://crm.hawkins.es/autoseo/reports/" class="cta-button" target="_blank">
                        🚀 Ver Mi Informe
                    </a>
                </div>

                <p style="margin-top: 30px;">Si tienes alguna pregunta sobre tu informe, no dudes en contactarnos.</p>
                
                <p style="color: #6b7280; font-size: 0.95rem;">Saludos,<br><strong>Equipo Hawkins</strong></p>
            </div>

            <div class="footer">
                <p>Este es un correo automático del sistema SEO de Hawkins.</p>
                <p>&copy; {{ date('Y') }} Hawkins Digital - Todos los derechos reservados</p>
                <p style="margin-top: 15px;">
                    <a href="https://hawkins.es" style="color: #6366f1; text-decoration: none;">hawkins.es</a> | 
                    <a href="https://hawkins.es/contacto" style="color: #6366f1; text-decoration: none;">Contacto</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
