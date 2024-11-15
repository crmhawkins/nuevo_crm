<?php $lineCounterForBreak = 0;?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta charset="ISO-8859-1">
        <title>{{ $data['title'] }}</title>

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
            table {
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
            .logo {

                padding-bottom: 0px;
            }
            .information table {
                padding: 10px;
                padding-bottom: 0px;
            }
            .projectConceptRow {
                border-collapse: collapse;
            }
            .projectConceptRow{
                border: 1px solid #aaaaaa;
            }
            .projectConceptRow th {
                border: 1px solid #aaaaaa;
            }
            .projectConceptRow td {
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
        <div class="information" style="position: fixed;margin-top:-90px">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%;padding-left:20px;vertical-align:bottom;">
                        <h1 style="font-weight: normal;vertical-align:bottom"><cite>PRESUPUESTO</cite></h1>
                    </td>
                    <td align="right" style="width: auto;padding-left:20px;padding-right:45px">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1JSTbvPQy4RdU-Av5a1Rv6JdYIZZrRrhbCA&s" alt="Logo" width="200" class="logo"/>
                    </td>
                </tr>
            </table>
        </div>
        <div class="information" style="">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%;padding-left:20px;vertical-align:top">
                        <p style="font-size:12px">Ref.:<span style="padding-left:72px;font-weight: bold;">{{ $budget->reference }}</span></p>
                        <p style="font-size:12px">Versión: <span style="padding-left:46px;"></span></p>
                        <p style="font-size:12px">Fecha Envío: <span style="padding-left:17px;">{{ Carbon\Carbon::parse($budget->creation_date)->format('d/m/Y') }}</span></p>
                    </td>
                    <td align="right" style="width: 60%;padding-right:20px;">
                        <div style="border-style: solid;border-width: 1px;padding-left:15px;text-align:left;padding-bottom:15px;">
                                <h3>{{ $budget->cliente->company ?? $budget->cliente->name }}</h3>
                                <span style="text-align: left;font-size:12px">{{ $budget->cliente->address }}</span>
                                <br>
                                <span style="text-align: left;font-size:12px">{{ $budget->cliente->city }}</span>
                                <br>
                                <span style="text-align: left;font-size:12px">{{ $budget->cliente->zipcode}}&nbsp;&nbsp;&nbsp;&nbsp;{{ $budget->cliente->province }}</span>
                                <br>
                                <span style="text-align: left;font-size:12px">NIF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $budget->cliente->cif }}</span>
                        </div>
                        <h4>Forma de pago: {{ $budget->metodoPago->name}}</h4>
                    </td>
                </tr>
            </table>
            <table class="projectConceptRow" style="width:100%;padding-left:30px;padding-right:30px">
                <tr >
                <td style="width:20%;padding-left:10px;">Campaña</td>
                    <td style="width:80%;padding-left:10px;">{{ $budget->proyecto->name }}</td>
                </tr>
                <tr >
                    <td style="width:20%;padding-left:10px;">Concepto</td>
                    <td style="width:80%;padding-left:10px;">

                        {{ $budget->concept }}
                    </td>
                </tr>
            </table>
        </div>
        <br/>

        {{-- <div class="information" style="position: fixed; bottom: -60px; left: 0px; right: 0px; height: 120px;" >
        <div class="information" style="position: absolute; bottom: 0;padding-left:30px;padding-right:30px;margin-top:25px" > --}}

        <footer class="information pagenum" style="page-break-after: avoid;position: fixed; bottom: -60px;padding-left:30px;padding-right:30px;height: 140px;" >
            <hr style="margin-bottom: 0.1em;border-style:inset;border-width: 0.5px;color:black">
            <table width="100%;margin-bottom:5px">
                <tr>
                    <td align="left" style="width: 50%;">
                        @if ($budget->is_ceuta)
                            THWORK 3000 SL B72284631 - Calle Delgado Serrano Nº1, 3ºD, 1ªOficina (Ceuta 51001)
                        @else
                            @if( Carbon\Carbon::parse($budget->created_at) >= Carbon\Carbon::parse("2021/02/01"))
                                THWORK 3000 SL B72284631 C/General Primo de Rivera s/N  CP 11201 Algeciras (Cádiz)
                            @else
                                IPOINT COMUNICACION MASIVA SL.CIF: B 72139868 - Urb. Parque del Oeste nº5 11205 Algeciras (Cádiz)
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width: 50%;">
                        @if( Carbon\Carbon::parse($budget->created_at) >= Carbon\Carbon::parse("2021/02/01"))
                            {{-- BBVA: ES90 0182 3232 2802 0161 2178 --}}
                            BANKINTER: ES84 0128 0733 2001 0007 1396
                        @else
                            Santander: ES81 0049 1672 4225 10049483
                        @endif
                        </td>
                    </tr>
            </table>
        </footer>
        <main style="page-break-inside: always; margin-bottom:70px;margin-top:10px;">
            <div style="page-break-before: avoid;" >
                <div class="invoice" style="padding-left:16px;">
                    <table id="conceptTable" class="fixed" width="100%" style="width:100%;">
                        <tr>
                            <th  style="width:60%;text-align:left;">Concepto</th>
                            <th  style="width:20%;text-align:right">Uds.</th>
                            <th  style="width:20%;text-align:right">Precio/Uds.</th>
                            <th  style="width:20%;text-align:right">Subtotal</th>
                            <th  style="width:20%;text-align:right">Dcto.</th>
                            <th  style="width:20%;text-align:right">TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                            <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>


                            @foreach($budgetConceptsFormated as $concept)
                                <tr style="padding-right:0px;margin-top:100px;;">
                                    <td style="font-weight: bold">
                                        {{ $concept['title'] }}
                                    </td>
                                    <td style="text-align:right;vertical-align: top;">{{ $concept['units'] }}</td>
                                    <td style="text-align:right;vertical-align: top;">{{ $concept['unit_price'] ?? 0  }} &nbsp;€</td>
                                    <td style="text-align:right;vertical-align: top;">{{ $concept['subtotal'] }} &nbsp;€</td>
                                    <td style="text-align:right;vertical-align: top;">{{ $concept['discount'] }}</td>
                                    <td style="text-align:right;vertical-align: top;">{{ $concept['total'] }} &nbsp;€</td>
                                </tr>
                                @foreach($concept['description'] as $conceptDescriptionRow)
                                <tr style="page-break-inside: auto;">
                                    <td colspan="6">
                                        <span style="font-size:10px">{{$conceptDescriptionRow}}</span>
                                    </td>
                                </tr>
                                    @endforeach


                                 <tr><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><td></td><td></td><td></td><td></td><td></td></tr>

                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            <div style="page-break-after: avoid; height: 140px;padding-left:35px">
                 <br><br>
                @if($sumatorio != 'true')
                <table id="summary" style="border-collapse: collapse;width: 100%;">
                    <tr>
                        <th style="text-align:center">Bruto</th>
                        <th style="text-align:center">Dto.</th>
                        <th style="text-align:center">Base</th>
                        <th style="text-align:center">IVA {{ $budget->iva_percentage }}%</th>
                        <th style="text-align:right">TOTAL</th>
                    </tr>
                    <tr>
                        <td style="text-align:center">{{ $budget->gross }}&nbsp;€</td>
                        <td style="text-align:center">{{ $budget->discount }}&nbsp;€</td>
                        <td style="text-align:center">{{ $budget->base }}&nbsp;€</td>
                        <td style="text-align:center">{{ $budget->iva }}&nbsp;€</td>
                        <td style="text-align:right">{{ $budget->total }}&nbsp;€</td>
                    </tr>
                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
                    <tr>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:left;font-weight:bold">Conforme Cliente (firma y sello)</td>
                    </tr>
                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
                    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
                    <tr>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:center"></td>
                        <td style="text-align:left;font-weight:bold;border-bottom: 1px solid #000;">Fdo.</td>
                    </tr>
                </table>
                @endif
                @if (isset($budget->description))
                <h4>Observaciones</h2>
                <p><small>{{$budget->description}}</small></p>
                @endif
            </div>

            <div class="information" style="display: block; page-break-before: always; margin-top:110px;">
                    <div style="padding-left:30px; margin: 0;">
                        <div style="text-align: left;font-size:9px;text-align:justify; width: 48%; display:inline-block;">
                            <h3 style="margin-bottom: 0px;">Condiciones Generales</h3>
                                    <p style="font-size:7px;">
                                        Estas condiciones regulan la relación entre las dos partes del presente contrato.
                                        Hawkins (THWORK3000,s.l.) y el cliente, cuyos datos están en  la pagina 1 del presente contrato.
                                    </p>
                                    <h4 style="margin-bottom: 0px;">ACEPTACION GENERAL</h4>
                                    <p style="font-size:7px;">
                                        La aceptación del presente presupuesto, por cualquiera de los medios que nuestro departamento de gestión le ofrezca, es un reconocimiento implícito de que usted en representación de su empresa o de si mismo, conoce la totalidad de nuestros términos y condiciones y que está conforme con los mismos.
                                    </p>
                                    <h4 style="margin-bottom: 0px;">INTRODUCCION</h4>
                                    <p style="font-size:7px;">
                                        Estos Términos y Condiciones de Compra Estándar (STCP, por sus siglas en inglés) se aplicarán a todas las compras de bienes y servicios realizados a todas las empresas bajo la marca HAWKINS, cuyo domicilio social se encuentre en España y prevalecerán sobre cualquier término y condición de venta, a menos que se acuerde lo contrario entre las partes.
                                    </p>
                                    <h4 style="margin-bottom: 0px;">PRECIO</h4>
                                    <p style="font-size:7px;">
                                        El precio pactado queda recogido en el presente contrato, el cual una vez aceptado, se mantendrá invariable y no podrá ser modificado en ningún caso. Se mantendrá inamovible incluso si no existe conformidad por parte del cliente en la recepción del trabajo. Que podrá hacer uso de las reclamaciones disponibles en el apartado de Garantía.
                                        Hawkins, es el propietario único y legitimo del producto y/o servicio ofrecido en este contrato hasta el abono total del importe.
                                     </p>
                                    <h4 style="margin-bottom: 0px;">PLAZO DE EJECUCION</h4>
                                    <p style="font-size:7px;">
                                        Los plazos previstos en el presente contrato son estimaciones basadas en la experiencia de nuestra empresa, siendo por ello posibles variaciones en la fecha de entrega debido a picos de producción o problemas externos.
                                        Si la fecha de entrega es una condición sine qua non, deberá ser adjuntada al presente contrato como anexo, el cual deberá ser firmado por ambas partes.

                                    </p>
                                    <h4 style="margin-bottom: 0px;">GARANTIA GENERAL</h4>
                                    <p style="font-size:7px;">
                                        Todos los servicios de Hawkins ofrecen la garantía exigida por la legislación vigente en España.
                                        Para defectos de fabricación. Hawkins podrá ofrecer un descuento a su criterio o bien ofrecerá la retirada total del producto y la reposición de este, sin que ello conlleve una reducción del precio de venta.
                                        Hawkins no es responsable del uso indebido de las aplicaciones, grafismos, modelos y o cualquier otro servicio ofrecido.
                                        De igual forma no es responsable del robo de datos, hackeos, infecciones de virus y/o cualquier uso delictivo que se realice sobre cualquier desarrollo o servicio realizado por Hawkins. Siendo el cliente el responsable de su propio producto, el cual deberá proteger y actualizar para cumplir con las normativas de uso privadas y públicas.
                                    </p>
                                    <h4 style="margin-bottom: 0px;">GARANTIA Y PROCEDIMIENTO DEL DEPARTAMENTO DE DISEÑO</h4>
                                    <p style="font-size:7px;">
                                       El procedimiento a seguir en el departamento de creatividad grafica parte de la reunión inicial donde el cliente facilita a Hawkins la idea o briefing sobre el que iniciar el trabajo.
                                        Hawkins, independientemente del tipo de trabajo de diseño gráfico, realizará las modificaciones necesarias satisfacer las preferencias del cliente. Si estas modificaciones superan las 3 revisiones, Hawkins podrá unilateralmente rescindir el contrato sin cobro alguno en la partida de diseño.
                                    </p>
                                    <p style="font-size:7px;">
                                        En la elaboración de artes finales o “archivo final que se envía a impresión” El cliente debe hacer una revisión exhaustiva de que todo está a su gusto, sin faltas tipográficas, bailes de números y/o cualquier otro elemento que considere que no debe estar.
                                        El cliente es el único responsable si una vez impreso el diseño, este tiene elementos erróneos o que no son del agrado del cliente.
                                        Todos los trabajos de impresión de Hawkins están firmados salvo que el cliente exprese por escrito lo contrario.
                                        Si el cliente decide no terminar el diseño por los motivos que fuere, no podrá solicitar la devolución total ni parcial de las cantidades abonadas y quedará a criterio de Hawkins la reclamación de las cantidades.

                                    </p>
                                </div>
                                <div style="text-align: left;font-size:9px;text-align:justify; width: 48%; display:inline-block; margin-left:10px;">
                                    <h4>GARANTIA Y PROCEDIMIENTO DEL DEPARTAMENTO DE DESARROLLO</h4>
                                    <p style="font-size:7px;">
                                        El procedimiento del departamento de desarrollo se basa en los principios de briefing inicial- mapa de desarrollo – desarrollo. Por lo que el cliente podrá realizar los cambios que estime oportuno mientras el software esté en su etapa inicial de “Boceto” o “diseño de apariencia”
                                        Todas las modificaciones a realizar una vez el software esté en desarrollo, deberán presupuestarse independientemente.
                                        Las aplicaciones web, app y desktop incluyen todo lo que esté descrito en el presente presupuesto y/o anexos firmados. El resto de los elementos deberá presupuestarse independientemente.
                                        Si el cliente decide no terminar la aplicación por los motivos que fuere, no podrá solicitar la devolución total ni parcial de las cantidades abonadas y quedará a criterio de Hawkins la reclamación de las cantidades restantes.
                                    </p>
                                    <h4>GARANTIA Y PROCEDIMIENTO DEL DEPARTAMENTO DE 3D 4D y METAVERSO</h4>
                                    <p style="font-size:7px;">
                                        El cliente deber facilitar toda la información relativa al proyecto en formatos digitales y usables para un correcto desarrollo, además de facilitar toda la información estética y/o de memoria constructiva en el proceso inicial del proyecto.
                                        Los proyectos 3D y 4D podrán sufrir hasta 3 modificaciones sin sobrecoste.
                                        El cliente recibirá imágenes en resolución 4K mediante fichero nube.
                                        El metaverso no incluye el servidor host salvo que el presupuesto indique lo contrario.

                                        Si el cliente decide no terminar la aplicación por los motivos que fuere, no podrá solicitar la devolución total ni parcial de las cantidades abonadas y quedará a criterio de Hawkins la reclamación de las cantidades restantes.

                                    </p>
                                    <h4>GARANTIA Y PROCEDIMIENTO DEL DEPARTAMENTO RRSS</h4>
                                    <p style="font-size:7px;">
                                        Hawkins ofrecerá al cliente mediante comunicación mensual un plan detallado de publicaciones, el cual será aceptado.
                                    El cliente es el único responsable si el plan autorizado por el mismo genera algún perjuicio a terceros.
                                    Hawkins podrá negarse a la publicación de contenido que considere inapropiado incluso aunque la iguala del contrato esté vigente.
                                     </p>
                                     <p style="font-size:7px;">
                                    Las cantidades abonadas mediante iguala (fee) no serán devueltas en ninguna circunstancia, siendo el cliente la parte responsable del ritmo y calidad de las publicaciones.
                                    Hawkins a su vez se compromete a mantener una comunicación fluida donde se muestre con la periodicidad contratada los planes y estrategias de comunicación en redes sociales.
                                     </p>
                                    <h4>CONFIDENCIALIDAD</h4>
                                    <p style="font-size:7px;">
                                        No se considerará información privilegiada o confidencial ningún conocimiento o dato, relacionado con los Materiales o los Servicios abarcados en las OC, que el comprador haya revelado a Hawkins. , a no ser que Hawkins   acceda a ello de manera expresa y por escrito.
                                    </p>
                                    <h4>GARANTIA GENERAL</h4>
                                    <p style="font-size:7px;">
                                        Todos los servicios de Hawkins ofrecen la garantía exigida por la legislación vigente en España.
                                        Para defectos de fabricación. Hawkins podrá ofrecer un descuento a su criterio o bien ofrecerá la retirada total del producto y la reposición de este, sin que ello conlleve una reducción del precio de venta.
                                        Hawkins no es responsable del uso indebido de las aplicaciones, grafismos, modelos y o cualquier otro servicio ofrecido.
                                        De igual forma no es responsable del robo de datos, hackeos, infecciones de virus y/o cualquier uso delictivo que se realice sobre cualquier desarrollo o servicio realizado por Hawkins. Siendo el cliente el responsable de su propio producto, el cual deberá proteger y actualizar para cumplir con las normativas de uso privadas y públicas.
                                    </p>
                                    <h4>INTEGRIDAD DEL ACUERDO, MODIFICACIONES.</h4>
                                    <p style="font-size:7px;">
                                        Este contrato es la declaración final y completa de las condiciones del contrato entre las partes y sustituye a cualquier otro acuerdo o negociación anterior o actual, ya sea oral o escrita, en relación con su objeto. Esta Orden de compra solo puede ser modificada por escrito y con la firma de ambas partes.
                                    </p>

                                </div>
                    </div>
                </div>
        </main>
    </body>
</html>
