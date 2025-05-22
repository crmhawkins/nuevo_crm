
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
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
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"> Correo electronico enviado desde el CRM & ERP de Los Creativos de Hawkins </div>
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
                        <td bgcolor="#ffffff" align="left" valign="top" style="border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <img src="https://www.lchawkins.com/wp-content/uploads/2021/09/Logo_Hawkins_21.svg" width="170" height="100" alt="logo" style="display: block; border: 0px;" />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 400; letter-spacing: 3px; line-height: 48px;">
                            <p style="text-align: center;">@if($newsletter->first_title_newsletter) {{$newsletter->first_title_newsletter}} @else ¡En Hawkins te echamos de menos! @endif</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#000" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            @if($newsletter->promo[0])
                                <a href="{{$newsletter->urls[0]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[0])}}" alt="{{$newsletter->banner_description}}" width="600" style="display: block; border: 0px;" />
                                </a>
                            @else
                                <img src="https://dummyimage.com/600x400%20/000/fff" alt="banner" style="display: block; border: 0px;" />
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 700px;">
                    <tr>
                        <td  colspan="3" bgcolor="#ffffff" align="left" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                           <p>{{$newsletter->description}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[1])
                                <a href="{{$newsletter->urls[1]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[1])}}" width="250" alt="categoria1">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria1">
                            @endif
                        </td>
                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[2])
                                <a href="{{$newsletter->urls[2]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[2])}}" width="250" alt="categoria2">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria2">
                            @endif
                        </td>
                    
                    <tr>
                        <td bgcolor="#ffffff" align="center"style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[3])
                                <a href="{{$newsletter->urls[3]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[3])}}" width="250" alt="categoria3">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria3">
                            @endif
                        </td>
                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[4])
                                <a href="{{$newsletter->urls[4]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[4])}}" width="250" alt="categoria4">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria4">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[5])
                                <a href="{{$newsletter->urls[5]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[5])}}" width="250"alt="categoria5">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria5">
                            @endif
                        </td>
                        <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            @if($newsletter->promo[6])
                                <a href="{{$newsletter->urls[6]}}">
                                    <img src="{{ asset('images/'.$newsletter->promo[6])}}" width="250" alt="categoria6">
                                </a>
                            @else
                                <img src="https://dummyimage.com/250x160%20/000/fff" width="250" alt="categoria6">
                            @endif
                        </td>
                    </tr>
                    
                    <tr>
                        <td  colspan="2" bgcolor="#ffffff" align="left" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                           <p style="text-align: center;">Mucho más <a href="https://www.lchawkins.com/">aquí.</a></p>
                        </td>
                    </tr>
                    <tr>
                        <td  colspan="2" bgcolor="#ffffff" align="left" style="padding: 20px 30px 5px 30px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                           <p style="text-align: center;">¿Prefieres que te llamemos? <a href="tel:+34956662942">Click aquí.</a></p>
                           <p>Nuestros clientes nos valoran  con 5 estrellas y ese, es nuestro mayor orgullo.</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#ffffff" align="left" style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <p style="margin: 0;">Un cordial saludo,<br>Los Creativos de Hawkins</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#ffffff" align="center" style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #000; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 12px; font-weight: 400; line-height: 25px;">
                            <a href="https://crmhawkins.com/unsubscribe" target="_blank">Darse de baja</a><br>
                            <a href="{{ asset('assets/clausulas.pdf') }}" target="_blank">Politica de privacidad</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <img src='https://crmhawkins.com/checkEmail/{{$newsletter->id_newsletter}}' width='1' height='1' border='0' alt='' hidden>
</body>

</html>