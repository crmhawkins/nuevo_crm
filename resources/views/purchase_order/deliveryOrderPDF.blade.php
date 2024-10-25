<?php $lineCounterForBreak = 0;?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta charset="ISO-8859-1">
        <title>{{ $data['title_budget'] }}</title>

        <style type="text/css">
            @page {
                margin: 0px;
            }
            @page { margin-top: 120px; }
            body {
                margin: 0px;
                padding-top:20px;
            }
            * {
                font-family: Verdana, Arial, sans-serif;
            }
            a {
                color: #fff;
                text-decoration: none;
            }
            .logo{
                font-size: x-small;
            }

            .order{
                font-size: x-small;
            }

            .orderConcept {
                font-size: x-small;
                border-spacing: 18px;
                border-collapse: separate;
            }

            .orderConcept td{
                padding: 5px;
            }

            .info td{
                padding: 5px;
            }

            .info{
                font-size: x-small;
            }

            tfoot tr td {
                font-weight: bold;
                font-size: x-small;
            }
            .invoice table {
                margin: 15px;
            }
            .invoice h3 {
                margin-left: 15px;
            }
            .information {
                /*background-color: #60A7A6;*/
                color: black;
            }
            .information .logo {
                margin: 5px;
            }
            .information table {
                padding: 10px;
                padding-bottom: 0px;
            }
            .orderConcept {
                border-collapse: collapse;
            }
            .orderConcept{
                border: 1px solid #aaaaaa;
            }
            .orderConcept th {
                border: 1px solid #aaaaaa;
            }
            .orderConcept td {
                border: 1px solid #aaaaaa;
            }
            table.fixed {
                table-layout:fixed;
            }
            /*table.fixed td { overflow: hidden; }*/
            #summary th, #summary td {
                text-align: left;
                padding: 8px;
            }

            /*#summary tr:nth-child(even){background-color: #f2f2f2}*/

            #summary th {
                background-color: black;
                color: white;
            }
            div.breakNow { page-break-inside:avoid; page-break-after:always; }

        </style>

    </head>
    <body style="padding-right:40px">
        <script type="text/php">
            if (isset($pdf)) {
              $font = $fontMetrics->getFont("Arial", "bold");
              $pdf->page_text(510, 753, "Página {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
            }
        </script>
        <div class="information logo" style="position: fixed;margin-top:-90px">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%;padding-left:20px;vertical-align:bottom">
                        <h1 style="font-size:19px;font-weight: normal;vertical-align:bottom"><cite>ALBARÁN DE ENTREGA</cite></h1>
                    </td>
                    <td align="right" style="width: 60%;padding-left:20px;padding-right:45px">
                        <img src="https://crm.hawkins.es/assets/images/logo/logo.png" alt="Logo" width="150" class="logo"/>

                    </td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-6">
                <h4>Datos del remitente</h4>
                <div class="information" style="">
                    <table class="info" style="width:100%;padding-left:30px;padding-right:30px">
                        <tr>
                            <td style="width:20%;padding-left:10px;">Empresa: </td>
                            <td style="padding-left:10px;">HAWKINS, (THWORK 3000 SL)</td>
                        </tr>
                        <tr>
                            <td style="width:20%;padding-left:10px;">NIF: </td>
                            <td style="padding-left:10px;">B72284631</td>
                        </tr>
                        <tr>
                            <td style="width:20%;padding-left:10px;">Direccions: </td>
                            <td style="padding-left:10px;">C/GENERAL PRIMO DE RIVERA S/N 11201 ALGECIRAS</td>
                        </tr>
                        <tr>
                            <td style="width:20%;padding-left:10px;">Telefono:</td>
                            <td style="padding-left:10px;">956 662 942</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-6">
                <h4>Dirección de entrega</h4>
                <div class="information" style="">
                    <table class="info" style="width:100%;padding-left:30px;padding-right:30px">
                        <tr>
                            <td style="width:20%;padding-left:10px;">Nombre del cliente:</td>
                            <td style="padding-left:10px;">{{$data['company']}} {{$data['client_name']}}</td>
                        </tr>
                        <tr>
                            <td style="width:20%;padding-left:10px;">Direccion: </td>
                            <td style="padding-left:10px;">{{$data['address']}} {{$data['city']}} {{$data['province']}} {{$data['cp']}}</td>
                        </tr>
                        <tr>
                            <td style="width:20%;padding-left:10px;">Telefono:</td>
                            <td style="padding-left:10px;">{{$data['phone']}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <br/>
        <div class="information" style="">
            <table class="orderConcept " style="border: 1px solid black;width:100%;padding-left:30px;padding-right:30px">
                <tr style="border: 0px;">
                    <td style="width:20%;border: 0px;padding-left:10px;">Ref.Orden: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['ref_order']}} </td>
                    <td style="width:20%;border: 0px;padding-left:10px;">Unidades: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['units']}} </td>
                    <td style="width:20%;border: 0px;padding-left:10px;">Titulo: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['title_budget']}}</td>
                </tr>
            </table>
        </div>

        <div class="main" style="">
            <table class="order" style="border: 0px solid black;width:100%;padding-left:30px;padding-right:30px">
                <tr style="border: 0px;">
                    <td style="font-size:16px;width:20%;border: 0px;padding-left:10px;text-decoration: underline;">Caracteristicas generales:</td>
                </tr>
                <tr>
                    <td style="width:20%;border: 0px;padding-left:10px">&nbsp;</td>
                </tr>
                <tr style="border: 0px;padding-top:10px;">
                    <td style="border: 0px;padding-left:10px">
                        {!! nl2br(e($data['concept_budget'])) !!}
                    </td>

                </tr>
            </table>
        </div>
    </body>
</html>
