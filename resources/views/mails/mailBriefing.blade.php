<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        h1 {
            font-size: 30px;
        }

        h2 {
            font-size: 25px;
        }

        h3 {
            font-size: 20px;
            border-bottom: 1px solid gray;
        }

        h4 {
            font-size: 15px;
        }

        @media screen {
            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 400;
                src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 700;
                src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 400;
                src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 700;
                src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
            }
        }

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* MOBILE STYLES */
        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }


        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <div
        style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        Correo electronico enviado desde el CRM & ERP de Los Creativos de Hawkins </div>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->
        <tr>
            <td bgcolor="#000" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#000" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top"
                            style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <img src="https://crm.hawkins.es/assets/images/logo/logo.png" width="350" height="200" alt="logo" style="display: block; border: 0px;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#ffffff" align="left"
                            style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <h1 style="padding-top: 40px;margin:0;">BRIEF DISEÑO WEB</h1>
                            <h2>Briefing de {{ $datos['nombre_empresa'] }}: </h2><br>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 0px 10px 0px 10px;">
                            <table>
                                <tr>
                                    <th colspan="2">Información sobre la Empresa o Negocio</th>
                                </tr>
                                <tr>
                                    <th>Nombre de la empresa</th>
                                    <td>{{ $datos['nombre_empresa'] }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción del negocio</th>
                                    <td> {{ $datos['descripcion_negocio'] }} </td>
                                </tr>
                                <tr>
                                    <th>Objetivos principales</th>
                                    <td> {{ $datos['objetivos'] }} </td>
                                </tr>
                                <tr>
                                    <th>Público objetivo</th>
                                    <td> {{ $datos['publico_objetivo'] }} </td>
                                </tr>
                                <tr>
                                    <th colspan="2">Diseño y Contenido</th>
                                </tr>
                                <tr>
                                    <th>Logotipo</th>
                                    <td>
                                        @if (isset($datos['logotipo']))
                                            <img src="{{ $message->embed(storage_path('app/' . $datos['logotipo'])) }}"
                                                alt="Archivo" style="max-height:20vw">
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Páginas y Secciones necesarias</th>
                                    <td> {{ $datos['descripcion_negocio'] }} </td>
                                </tr>
                                <tr>
                                    <th>Objetivos principales</th>
                                    <td> {{ $datos['objetivos'] }} </td>
                                </tr>
                                <tr>
                                    <th>Público objetivo</th>
                                    <td> {{ $datos['publico_objetivo'] }} </td>
                                </tr>
                                <tr>
                                    <th>Páginas y Secciones necesarias</th>
                                    <td> {{ $datos['paginas_secciones'] }} </td>
                                </tr>
                                <tr>
                                    <th>Ejemplo de sitio web 1</th>
                                    <td> {{ $datos['ejemplo_sitio1'] }} </td>
                                </tr>
                                <tr>
                                    <th>Ejemplo de sitio web 2</th>
                                    <td> {{ $datos['ejemplo_sitio2'] }} </td>
                                    </tr>
                                <tr>
                                    <th>Ejemplo de sitio web 3</th>
                                    <td> {{ $datos['ejemplo_sitio3'] }} </td>
                                </tr>
                                <tr>
                                    <th>Contenido multimedia</th>
                                    <td>
                                        @if (isset($datos['contenido_multimedia']))
                                            @foreach ($datos['contenido_multimedia'] as $path)
                                                <img src="{{ $message->embed(storage_path('app/' . $path)) }}"
                                                    alt="Contenido multimedia" style="max-height:20vw">
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr><th colspan="2">Funcionalidad y Técnica</th></tr>
                                <tr>
                                <th>Idiomas del sitio</th>
                                <td> {{ $datos['idiomas'] }} </td>
                            </tr>
                            <tr>
                                <th>Integraciones requeridas</th>
                                <td> {{ $datos['integraciones'] }} </td>
                            </tr>
                            <tr>
                                <th>Registro de usuarios</th>
                                <td> {{ $datos['registro_usuarios'] }} </td>
                            </tr>
                            <tr>
                                <th>Formularios específicos</th>
                                <td> {{ $datos['formularios_especificos'] }} </td>
                            </tr>
                            <tr>
                                <th>Optimización para móviles</th>
                                <td> {{ $datos['optimizacion_moviles'] }} </td>
                            </tr>
                            <tr>
                                <th>SEO</th>
                                <td> {{ $datos['seo'] }} </td>
                            </tr>
                            <tr>
                                <th colspan="2">Dominio y Hosting</th>
                            </tr>
                            <tr>
                                <th>Dominio</th>
                                <td> {{ $datos['dominio'] }} </td>
                            </tr>
                            <tr>
                                <th>Hosting</th>
                                <td> {{ $datos['hosting'] }} </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        <tr>
            <td bgcolor="#ffffff" align="left"
                style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                <p style="margin: 0;">Un cordial saludo,<br>Los Creativos de Hawkins</p>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    <!--
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#FFECD1" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">Need more help?</h2>
                            <p style="margin: 0;"><a href="#" target="_blank" style="color: #FFA73B;">We&rsquo;re here to help you out</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#f4f4f4" align="left" style="padding: 0px 30px 30px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;"> <br>
                            <p style="margin: 0;">If these emails get annoying, please feel free to <a href="#" target="_blank" style="color: #111111; font-weight: 700;">unsubscribe</a>.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    -->
    </table>
</body>

</html>

