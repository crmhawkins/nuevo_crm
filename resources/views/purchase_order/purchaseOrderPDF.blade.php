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
        <div class="information logo" style="margin-top:-130px">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%;padding-left:20px;vertical-align:bottom">
                        <h1 style="font-size:15px;font-weight: normal;vertical-align:bottom; width:100%"><cite><strong>ORDEN DE COMPRA Nº {{$data['ref_order']}}</strong> </cite></h1>
                    </td>
                    <td align="right" style="width: 60%;padding-left:20px;padding-right:45px">
                        <img src="https://crm.hawkins.es/assets/images/logo/logo.png" alt="Logo" width="150" class="logo"/>

                    </td>
                </tr>
            </table>
        </div>
        <div class="information" style="">
            <table class="info" style="width:100%;padding-left:30px;padding-right:30px">
                <tr>
                    <td style="width:20%;padding-left:10px;">Agencia: </td>
                    <td style="padding-left:10px;"> {{$data['name']}} </td>
                </tr>
                <tr>
                    <td style="width:20%;padding-left:10px;">Proveedor: </td>
                    <td style="padding-left:10px;"> {{$data['supplier']}}</td>
                </tr>
                <tr>
                    <td style="width:20%;padding-left:10px;">Fecha de envio: </td>
                    <td style="padding-left:10px;"> {{$data['date']}}</td>
                </tr>

            </table>
        </div>
        <br/>
        <div class="information" style="">
            <table class="orderConcept " style="border: 1px solid black;width:100%;padding-left:30px;padding-right:30px">
                <tr style="border: 0px;">
                    <td style="width:20%;border: 0px;padding-left:10px;">Ref.Orden: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['ref_order']}} </td>
                    <td style="width:20%;border: 0px;padding-left:10px;">Unidades: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['units']}} </td>
                </tr>
                <tr style="border: 0px;">
                    <td style="width:20%;border: 0px;padding-left:10px;">Titulo: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['title_budget']}}</td>
                    <td style="width:30%;border: 0px;padding-left:10px;">Importe compra: </td>
                    <td style="width:75%;border: 0px;padding-left:10px;"> {{$data['import_order']}} </td>
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


        <!--
        <div class="information" style="position: fixed; bottom: -60px; left: 0px; right: 0px; height: 120px;" >
        <div class="information" style="position: absolute; bottom: 0;padding-left:30px;padding-right:30px;margin-top:25px" >
        -->
        <footer class="information pagenum" style="page-break-after: avoid;position: fixed; bottom: -60px;padding-left:30px;padding-right:30px;height: 140px;" >
            <hr style="margin-bottom: 0.1em;border-style:inset;border-width: 0.5px;color:black">
            <table width="100%;margin-bottom:5px; font-size:14px">
                <tr>
                    <td align="left" style="width: 50%;">
                        {{  $budgetCustomPDF->footer_string_1}}
                    </td>
                    <!--
                        <td align="right" style="width: 50%;">
                            Company Slogan
                        </td>
                    -->
                </tr>
                <tr>
                    <td align="left" style="width: 50%;">
                            {{  $budgetCustomPDF->footer_string_2}}
                        </td>
                    </tr>
            </table>
        </footer>
        <div class="information" style="display: block; page-break-before: always;">
            <table class="order" style="border: 0px solid black;width:100%;padding-left:30px;padding-right:30px; font-size: 10px">
                <tr style="border: 0px;">
                    <td style="font-size:16px;width:20%;border: 0px;padding-left:10px;text-decoration: underline;">Condiciones de la orden de compra:</td>
                </tr>
                <tr>
                    <td style="width:20%;border: 0px;padding-left:10px">&nbsp;</td>
                </tr>
                <tr style="border: 0px;padding-top:10px;">
                    <td style="border: 0px;padding-left:10px">
                        <h3>1. ACEPTACIÓN GENERAL</h3>
                        <p>La parte de la que THWORK3000,S.L.  obtiene los materiales o los servicios ("Vendedor") accede a prestar los servicios solicitados ("Servicios") y/o a proporcionar los materiales solicitados ("Materiales") descritos en una orden de compra/solicitud de presupuesto ("Orden de compra") de acuerdo con estas condiciones. Tras la aceptación de esta Orden de compra, el envío de los Materiales o el inicio de los Servicios, el Vendedor quedará vinculado a las disposiciones de esta Orden de compra, lo que incluye todas las disposiciones establecidas en el anverso de esta.
                        </p>
                        <p>Todas las compras realizadas por THWORK3000,S.L. ("THWORK3000,S.L.") al amparo de este acuerdo tendrán lugar de conformidad con las Órdenes de compra ("OC") emitidas y regidas por estas condiciones. TODAS ESTAS OC SE REGIRÁN EXCLUSIVAMENTE POR ESTAS CONDICIONES. Por el presente documento, se rechaza cualquier condición adicional o contradictoria que pudiera existir en cualquier recibo, factura o comunicado del Vendedor. En caso de incoherencia entre las disposiciones de cualquier OC y de cualquier documento o acuerdo a los que se haga referencia de aquí en adelante o que las partes hayan acordado por separado, dichas incoherencias deberán resolverse con el siguiente orden de prioridad:
                        </p>
                        <p>i.	Un contrato escrito independiente;</p>
                        <p>La OC en la que se hace referencia a estas condiciones o en la que estas se adjuntan;</p>
                        <p>iii. Estas condiciones;</p>
                        <p>iv.	Planos;</p>
                        <p>v.	Especificaciones, y</p>
                        <p>vi.	Cualquier otro documento incorporado como referencia.</p>

                        <h3>2. ENTREGA, EMBALAJE Y ENVÍO</h3>
                        <p>La entrega de los Materiales se realizará según lo expuesto en la OC, a no ser que se haya acordado lo contrario de manera expresa, y debe realizarse según lo especificado en el anverso de esta Orden de compra con respecto al calendario, al transportista y a las instalaciones de THWORK3000,S.L.  . Todo ello se llevará a cabo sin ningún cargo por el empaquetado, el embalaje, el transporte o el almacenamiento, a no ser que se especifique lo contrario. Si el documento no especifica ningún calendario de entrega, el Vendedor efectuará la entrega empleando el método de transporte terrestre más rápido posible. Si la Orden de compra no especifica ningún método de envío, el Vendedor utilizará el transportista más económico. EL TIEMPO ES EXTREMADAMENTE IMPORTANTE EN EL MOMENTO DE REALIZAR LA ENTREGA. El Vendedor notificará de inmediato a THWORK3000,S.L.   si sospecha o es consciente de alguna circunstancia que pudiera impedir la entrega puntual de los Servicios o Materiales solicitados. Tras dicha notificación, THWORK3000,S.L.   podrá, si así lo desea:
                        </p>
                        <p>i. Negarse a aceptar los Materiales o Servicios y cancelar la Orden de compra;</p>
                        <p>i. Exigir una parte justa y proporcional de los Materiales disponibles del Vendedor y cancelar el resto de la Orden de compra, o</p>
                        <p>ii. Solicitar la entrega por el medio más rápido para cumplir los plazos de entrega a cargo exclusivo del Vendedor.</p>

                        <p>El Vendedor realizará el embalaje de todos los Materiales en recipientes adecuados para una manipulación y un transporte seguros, los asegurará contra daños causados por el transporte o las inclemencias del tiempo y garantizará los costes de transporte más bajos posibles. Dichos recipientes estarán debidamente etiquetados e incluirán un albarán con todos los artículos y su correspondiente número de partida en la OC. El número de la Orden de compra de THWORK3000,S.L.   debe aparecer en todos los recipientes, embalajes, albaranes y conocimientos de embarque.</p>
                        <p>Si el envío se realizase directamente al cliente final, el embalaje deberá ir personalizado con el logotipo de THWORK3000,S.L.  quedando eliminado cualquier marca o referencia al vendedor.</p>


                        <h3>3. RIESGO DE PÉRDIDA; DESTRUCCIÓN DE MATERIALES.</h3>
                        <p>El Vendedor asume todo riesgo relacionado con los Materiales que se incluyen en esta Orden de compra hasta que THWORK3000,S.L.   los reciba en el destino designado. Si los Materiales incluidos en esta Orden de compra se destruyen antes de que THWORK3000,S.L.   los reciba, THWORK3000,S.L.   podrá:</p>
                        <p>a. Cancelar esta Orden de compra o</p>
                        <p>b. Solicitar la entrega por parte del Vendedor en cuanto sea posible, desde un punto de vista comercial y práctico, sustituir los Materiales por otros equivalentes en cuanto a cantidad y calidad.</p>
                    </td>
                </tr>
                <tr>
                    <td style="border: 0px;padding-left:10px">


                        <h4>1. INSPECCIÓN</h4>
                        <p>En momentos en los que sea razonable, THWORK3000,S.L.   podrá inspeccionar, en las instalaciones del Vendedor o de un subcontratista del Vendedor, los Materiales y Servicios que entregar incluidos en esta Orden de compra. La inspección de los Materiales por parte de THWORK3000,S.L.   antes o después de la entrega no constituye una aceptación de estos. Ninguna inspección o prueba realizadas antes de la aceptación definitiva eximirá al Vendedor de su responsabilidad ante los defectos u otros fallos que pudieran impedir el cumplimiento de los requisitos de la Orden de compra de THWORK3000,S.L.</p>
                        <p>Los Materiales incluidos en esta Orden de compra quedarán sujetos a una inspección y aprobación definitivas por parte de THWORK3000,S.L.   dentro de un plazo de tiempo razonable tras la entrega a THWORK3000,S.L.  , independientemente de los pagos que THWORK3000,S.L.   haya podido realizar antes de la entrega. </p>
                        <p>THWORK3000,S.L.   puede rechazar o revocar la aceptación de cualquier Material defectuoso, con defecto de fabricación o que no cumpla las especificaciones de THWORK3000,S.L.  . THWORK3000,S.L.   puede decidir:</p>
                        <p>a. Devolver los Materiales rechazados por el importe completo de la factura más los gastos de transporte aplicables;</p>

                    </td>
                </tr>
                <tr>
                    <td style="border: 0px;padding-left:10px">
                        <p>b. Conservar los Materiales rechazados para que los repare el vendedor o, a elección de THWORK3000,S.L.  , los repare THWORK3000,S.L.   con la ayuda del Vendedor, según lo solicite THWORK3000,S.L.   de manera razonable, o</p>
                        <p>c. Devolver los Materiales rechazados al Vendedor para su reparación o sustitución dentro del plazo de tiempo que THWORK3000,S.L.   requiera de manera razonable. THWORK3000,S.L.   podrá recuperar el importe total de los costes y gastos, la pérdida de valor y cualquier otro daño en el que pueda incurrir en relación con la reparación o sustitución de los Materiales o Servicios que no cumplan las especificaciones, ya sea mediante una reducción equivalente en el precio o mediante la reducción de los importes que, por otros conceptos, THWORK3000,S.L.   le deba al Vendedor.</p>

                        <h4>2. GARANTÍA</h4>
                        <p>El Vendedor garantiza lo siguiente de forma expresa:</p>
                        <p>a. El Vendedor es el titular legítimo de todos los Materiales que THWORK3000,S.L.   debe comprar en virtud de este documento y estos están libres de cargas, reclamaciones o cualquier otro gravamen.</p>
                        <p>b. Los Materiales o Servicios proporcionados se atendrán a todas las especificaciones, planos, muestras u otras descripciones aplicables proporcionados, especificados o adoptados por THWORK3000,S.L.  , y a todos los demás requisitos de la OC.</p>
                        <p>c. Los Materiales son de calidad comercial, se han producido empleando unos materiales y una mano de obra confiables, y estarán libres de defectos durante al menos un año desde la fecha de entrega a THWORK3000,S.L.  , o durante el periodo de garantía superior a un año que THWORK3000,S.L.   especifique en la OC.</p>
                        <p>d. Todos los Materiales incluidos en la OC han sido seleccionados, diseñados, fabricados o montados por el Vendedor con base en el uso previsto de THWORK3000,S.L.   y serán adecuados y suficientes para ese fin. Dichas garantías (junto con las garantías de rendimiento de servicio anteriores y otras si las hubiese) permanecerán vigentes tras inspecciones, pruebas, aceptaciones y pagos de los Materiales o Servicios, y se aplicarán a THWORK3000,S.L.   y sus sucesores, cesionarios, clientes a cualquier nivel y todos los usuarios finales. El Vendedor también garantiza que todos los productos fabricados y vendidos, así como los Servicios provistos, a THWORK3000,S.L.   están libres de reclamaciones u obligaciones ante cualquier tercero por supuesto uso indebido, malversación o violación de patentes, marcas registradas, derechos de autor o de cualquier otro derecho. El Vendedor accede a defender (a su cargo, lo que incluye el pago de las costas y las tasas y pagos a abogados) cualquier reclamación, cargo o demanda legal realizada por cualquier parte contra THWORK3000,S.L.   o sus clientes a causa de las garantías anteriores o en relación con ellas o a cualquier incumplimiento de las mismas. También accede a indemnizar y liberar de toda responsabilidad a THWORK3000,S.L.   y a cualquiera de sus representantes ante dichas reclamaciones, demandas, responsabilidades, pérdidas, indemnizaciones, compensaciones, multas, acuerdos, costas legales, tasas de abogados y gastos en los que se incurra por dichas reclamaciones, cargos o demandas legales. El Vendedor notificará a THWORK3000,S.L.   por escrito de todos los avisos de reclamaciones que conozca. Si la presunta infracción está relacionada con la reclamación de un tercero con base en sus derechos de propiedad intelectual, el Vendedor (a su cargo) obtendrá el derecho a continuar usando el artículo, mecanismo, material, pieza, dispositivo, proceso o método para THWORK3000,S.L.  , lo sustituirá por un reemplazo que no incumpla ningún derecho —si su rendimiento no se va a ver afectado—, lo modificará de manera que ya no incumpla ningún derecho o lo retirará y devolverá el precio de compra, transporte e instalación del mismo.</p>

                        <h4>3. FACTURACIÓN</h4>
                        <p>El Vendedor deberá enviar las facturas a THWORK3000,S.L.   (a la atención del Departamento de administracion “ADMINISTRACIÓN@LCHAWKINS.COM) por los Materiales o Servicios incluidos en esta Orden de compra de manera inmediata tras realizar el envío de estos. Los datos de cada partida, las descripciones de los Materiales o Servicios y los números de referencia de las facturas del Vendedor deben coincidir con sus datos correspondientes en el anverso de esta Orden de compra. Las condiciones de pago estándar de THWORK3000,S.L.   son de 60 días después del día 1 del mes siguiente a la recepción de la factura mediante pagaré o confirming.</p>
                        <p>THWORK3000,S.L.   abonará al Vendedor la menor de las siguientes dos cantidades:</p>

                    </td>
                </tr>
                <tr>
                    <td>
                    <ul>
                            <li>a. La cantidad acordada y especificada en la Orden de compra/acuerdo o</li>
                            <li>b. El precio ofertado por el Vendedor en la fecha del envío (en el caso de Materiales) o en la fecha del inicio del trabajo (en el caso de Servicios). El Vendedor deberá especificar por separado en sus facturas los impuestos y otros cargos aplicables, como costes de envío, IVA, aranceles, costes aduaneros, tarifas, tributos y recargos exigidos por el estado.</li>
                        </ul>

                        <h4>4. PAGO</h4>
                        <p>En todo momento, THWORK3000,S.L.   tendrá derecho a descontar cualquier cantidad debida por el Vendedor, o cualquiera de sus empresas afiliadas, a THWORK3000,S.L.   de cualquier cantidad a pagar por THWORK3000,S.L.</p>
                        <p>El precio de los Materiales son los que se indican en las OC correspondientes y no están sujetos a ningún aumento durante el periodo del acuerdo de suministro. No se permitirá ningún aumento de precio de ningún tipo a no ser que THWORK3000,S.L.   lo haya aceptado de forma expresa y por escrito. A menos que se estipule expresamente lo contrario, el Vendedor correrá con todos los gastos en los que él incurra para suministrar los Materiales y/o Servicios.</p>

                        <h4>5. CAMBIOS EN LA ORDEN DE COMPRA</h4>
                        <p>En cualquier momento que lo desee, THWORK3000,S.L.   podrá modificar los siguientes conceptos de la Orden de compra: planos, diseños, especificaciones, envíos, embalaje, lugar para las inspecciones, lugar de entrega, lugar de aceptación, ajustes en cantidades, ajustes en los plazos de entrega o cantidad de material que THWORK3000,S.L.   deba proporcionar. El Vendedor notificará a THWORK3000,S.L.   de inmediato si hubiese algún cambio en los costes o los plazos previstos de finalización/entrega de los Servicios o Materiales incluidos en este documento como resultado de las modificaciones que THWORK3000,S.L.   haya realizado sobre la Orden de compra. Asimismo, el Vendedor entregará a THWORK3000,S.L.   una propuesta de ajuste de precios (con información de ayuda) en un plazo no superior a los 30 días desde la fecha en la que el Vendedor reciba la orden de compra modificada de THWORK3000,S.L.</p>


                        <h4>6. CAMBIOS DEL VENDEDOR</h4>
                        <p>El Vendedor no realizará ningún cambio en las especificaciones o la composición física de los Materiales incluidos en este documento, ni en los procesos empleados para fabricarlos, sin el consentimiento previo y por escrito de THWORK3000,S.L.</p>

                        <h4>7. INDEMNIZACIÓN</h4>
                        <p>El Vendedor accede a indemnizar, liberar de toda responsabilidad y —a petición de THWORK3000,S.L.  — defender a THWORK3000,S.L.   y sus ejecutivos, directores, clientes, representantes y empleados (cada uno de ellos, una "Parte indemnizada") contra cualquier reclamación, responsabilidad, daño, pérdida y gasto —lo que incluye tasas y cargos de abogados— en los que una Parte indemnizada haya incurrido por omisión del Vendedor o de sus empleados, representantes o subcontratistas de cualquier manera relacionada con los Materiales o Servicios provistos al amparo de esta Orden de compra. Esto incluye, entre otras cosas:</p>
                        <ul>
                            <li>a. Cualquier reclamación basada en la muerte o lesión de cualquier persona, la destrucción o los daños a la propiedad o la contaminación del medio ambiente.</li>
                            <li>b. Cualquier reclamación basada en el documento n.º 000393 REV C, la negligencia, omisión o falta grave intencional del Vendedor o de sus empleados, representantes o subcontratistas.</li>
                            <li>c. Cualquier reclamación de un tercero contra cualquier Parte indemnizada en la que se alegue que los Materiales, los Servicios, los resultados de dichos Servicios o de cualquier otro proceso abarcado en esta Orden de compra incumplen patentes, derechos de autor, derechos de marcas registradas, secretos comerciales o cualquier otro derecho de propiedad de un tercero, sea que estos se hayan provisto de manera independiente o en combinación con los Materiales o Servicios. El Vendedor no llegará a un acuerdo por ninguna de estas reclamaciones sin el consentimiento previo de THWORK3000,S.L.  . El Vendedor accede a pagar o reembolsar todos los costes en los que una Parte indemnizada pueda incurrir al aplicar esta exención de responsabilidad, lo que incluye las tasas y cargos de abogados.</li>
                        </ul>

                        <p>Si la utilización por parte de THWORK3000,S.L.  , sus distribuidores o sus clientes de los Materiales y Servicios incluidos en esta Orden de compra queda prohibida o impedida de cualquier forma por acciones legales, el Vendedor, a su cargo exclusivo, deberá tomar alguna de las siguientes medidas:</p>
                        <ul>
                            <li>a. Proporcionar como reemplazo unos Materiales o Servicios completamente equivalentes y no infractores.</li>
                            <li>b. Modificar los Materiales o Servicios de manera que ya no incumplan ningún derecho o normativa a la vez que ofrezcan una funcionalidad completamente equivalente.</li>
                            <li>c. Obtener el derecho a continuar usando los Materiales o Servicios para THWORK3000,S.L. y sus distribuidores o clientes.</li>
                            <li>d. Devolver todos los importes pagados por los Materiales o Servicios infractores.</li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>8. CONFIDENCIALIDAD</h4>
                        <p>No se considerará información privilegiada o confidencial ningún conocimiento o dato, relacionado con los Materiales o los Servicios abarcados en las OC, que el Vendedor haya revelado a THWORK3000,S.L.  , a no ser que THWORK3000,S.L.   acceda a ello de manera expresa y por escrito. THWORK3000,S.L.   obtendrá dicha información libre de restricciones —con la excepción de una reclamación por violación de patentes— como parte de las contraprestaciones de la OC. Toda la información técnica o de cualquier otro tipo que el Vendedor haya adquirido o averiguado a través de las órdenes de compra de THWORK3000,S.L.  , o como resultado de su relación con esta, es y seguirá siendo valiosa y confidencial, y debe tratarse como información privilegiada de THWORK3000,S.L.  . Esto incluye, entre otras cosas, los planos, los datos, las especificaciones, los componentes, los conceptos, los diseños o las herramientas. En la medida en la que las partes hayan suscrito un acuerdo de confidencialidad ("NDA", por sus siglas en inglés) para proteger dicha información, las disposiciones de dicho NDA regirán cualquier término contradictorio o incongruente en este documento. El Vendedor accede a no revelar a terceros la información privilegiada y confidencial de THWORK3000,S.L.  , y tampoco la usará para obtener ninguna ventaja o beneficio económico. El Vendedor acepta que cualquier mejora, modificación, reajuste o producto desarrollado por el Vendedor, o por el Vendedor y THWORK3000,S.L.   en conjunto, gracias al conocimiento de la información confidencial y privilegiada de THWORK3000,S.L.   será propiedad de THWORK3000,S.L.  , y deberá tratarse como información privilegiada y confidencial de THWORK3000,S.L.  . El Vendedor proporcionará dichas cesiones, u otros traspasos, en la medida en la que esos activos precisen de documentos adicionales a estas Condiciones. En consecuencia, el Vendedor acepta que THWORK3000,S.L.   tenga derecho a solicitar —además de cualquier otro recurso disponible en derecho o equidad— medidas cautelares para aplicar estas condiciones. Al cancelar o rescindir cualquier OC, la colaboración profesional entre las partes o de cualquier otro modo a petición de THWORK3000,S.L.  , el Vendedor deberá devolver a THWORK3000,S.L.   toda la información confidencial de este, lo que incluye las copias, los extractos y cualquier otra reproducción de dicha información. Las disposiciones de confidencialidad de este párrafo se aplicarán y serán vinculantes sobre los ejecutivos, directores, asesores, consultores y demás representantes. La protección de la confidencialidad de la información y los materiales de THWORK3000,S.L.   permanecerá expresamente vigente de manera indefinida tras cualquier cancelación o finalización de la colaboración entre las partes y hasta que dicha información deje de ser privilegiada o confidencial, a menos que THWORK3000,S.L.   proporcione un consentimiento por escrito indicando lo contrario.</p>


                        <h4>9. DERECHOS DE PROPIEDAD INTELECTUAL, PRODUCTO DEL TRABAJO</h4>
                        <p>En caso de que el Vendedor realice actividades de investigación, desarrollo o diseño utilizando la información proporcionada por THWORK3000,S.L.  , el Vendedor acepta que THWORK3000,S.L.   poseerá en exclusiva los derechos, la titularidad y los intereses sobre cualquier producto de trabajo resultante, lo que incluye, entre otras cosas, la propiedad intelectual, los secretos comerciales y los conocimientos especializados. El Vendedor tomará todas las medidas necesarias para garantizar que THWORK3000,S.L.   obtenga la completa titularidad legal sobre dichos derechos. El Vendedor garantizará que sus empleados, representantes y subcontratistas renuncien, de la manera adecuada, a realizar cualquier reclamación por todos los derechos e intereses sobre cualquier producto creado en relación con esta Orden de compra y asignen dichos derechos e intereses a THWORK3000,S.L.  . El Vendedor otorga a THWORK3000,S.L.   el derecho de crear copias, reproducciones o trabajos derivados del material provisto bajo este acuerdo y con el fin descrito en el mismo. El Vendedor no descompilará, ocultará ni aplicará ingeniería inversa sobre ningún tipo de tecnología, software, material, producto o artículo propiedad de THWORK3000,S.L.   o proporcionado por esta.</p>

                        <h4>10.	SEGURO</h4>
                        <p>Para los Servicios ofrecidos durante el término del acuerdo, lo que incluye cualquier obligación de la garantía, el Vendedor proporcionará y mantendrá los siguientes seguros (y con respecto a las pólizas basadas en reclamaciones, durante otros 24 meses):</p>
                        <ul>
                            <li>a. Responsabilidad Civil de los Empleadores, hasta 1 000 000 € por evento.</li>
                        </ul>
                    <h4>11.	RESCISIÓN POR CAUSA JUSTIFICADA</h4>
                        <p>Si el vendedor:</p>
                            <ul>
                                <li>1. Pasa a ser insolvente al tiempo del vencimiento de sus deudas o pasa a ser el sujeto de una declaración de quiebra;</li>
                                <li>2. Si cambia de propietario o titular de tal manera que la competencia de THWORK3000,S.L. gane una titularidad o un interés mayoritario en el Vendedor, o</li>
                                <li>3. Si falta a su deber de manera significativa con relación a cualquier disposición del acuerdo de suministro, THWORK3000,S.L.   podrá, a su discreción, rescindir esta Orden de compra, o cualquier otra, por "causa justificada" mediante un aviso a tal efecto con treinta (30) días de antelación y por escrito al Vendedor. Si el Vendedor, dentro del plazo de treinta días, corrige la causa que ha dado lugar a la notificación de manera satisfactoria para THWORK3000,S.L.  , esta podrá anular la rescisión. En caso de que THWORK3000,S.L.   rescinda una orden de compra de conformidad con esta sección, THWORK3000,S.L.   tendrá todos los derechos y recursos disponibles en derecho y equidad, y no tendrá ninguna otra obligación ante el Vendedor. Tras la rescisión, el Vendedor entregará de inmediato (a su cargo) a THWORK3000,S.L.   toda la información confidencial de THWORK3000,S.L.  , el producto del trabajo y las demás herramientas y bienes propiedad de THWORK3000,S.L.   que estén en posesión del Vendedor.</li>
                            </ul>


                    </td>
                </tr>
                <tr>
                    <td>

                        <h4>1. RESCISIÓN POR CONVENIENCIA</h4>
                        <p>THWORK3000,S.L.   podrá rescindir toda esta Orden de compra, o parte de ella, enviando un aviso al Vendedor, que entraría en vigor en la fecha especificada en la notificación. Al recibir el aviso de rescisión de THWORK3000,S.L.  , el Vendedor parará todos los trabajos y tomará cualquier otra medida que THWORK3000,S.L.   especifique en dicho aviso, a fin de facilitar la rescisión de la Orden de compra o de la parte aplicable.</p>
                        <p>Tras la rescisión, THWORK3000,S.L.   no incurrirá en ningún otro coste adicional ni adquirirá ninguna otra responsabilidad hacia el Vendedor, con la excepción de los Materiales que ya se hayan entregado y/o los costes reales menos cualquier valor comercial adquirido hasta la fecha de rescisión. Pasados seis (6) meses tras recibir el aviso de rescisión de THWORK3000,S.L.  , el Vendedor no podrá enviar a THWORK3000,S.L.   ninguna reclamación de reembolso por los costes adquiridos por el Vendedor a causa de la rescisión por conveniencia. El Vendedor tendrá la obligación de mitigar los daños.</p>

                        <h4>2. MEDIDAS CAUTELARES</h4>
                        <p>Puesto que los daños causados por el incumplimiento de este acuerdo no pueden determinarse de manera sencilla y ya que el incumplimiento de este acuerdo puede resultar en daños irreparables para THWORK3000,S.L.   para los que una indemnización económica quizá no sea suficiente, por el presente documento el Vendedor accede a que se emita una resolución contra el Vendedor para impedir cualquier incumplimiento de este acuerdo o cualquier incumplimiento continuo de este acuerdo por parte del Vendedor, así como a que THWORK3000,S.L.   tome cualquier otra medida disponible por derecho o equidad.</p>

                        <h4>3. FUERZA MAYOR</h4>
                        <p>Ni el Vendedor ni THWORK3000,S.L.   serán responsables por ningún retraso o error al cumplir con sus obligaciones en esta Orden de compra en los casos y en la medida en que dichos retrasos o errores se deban a circunstancias que, desde un punto de vista razonable, están fuera del control de dicha parte. Esto incluye, entre otras cosas, incendios, inundaciones, accidentes, desastres naturales, guerras declaradas o no declaradas, disturbios, huelgas o cierres patronales, escasez de materiales o medios de transporte, incapacidad para obtener licencias de importación o exportación, actuaciones del gobierno o cualquier disposición o requisito de leyes, normativas, sentencias o normas.</p>


                        <h4>4. CONFLICTOS LABORALES</h4>
                        <p>El Vendedor avisará de inmediato a THWORK3000,S.L.   sobre cualquier conflicto o problema laboral que pueda afectar a la capacidad del Vendedor de entregar los Materiales o Servicios de conformidad con las condiciones de esta Orden de compra. THWORK3000,S.L.   no tendrá la obligación de reembolsar importe alguno al Vendedor por las pérdidas o costes adicionales en los que este pueda incurrir por causa de los conflictos laborales.</p>

                        <h4>5. LIMITACIÓN DE RECURSOS Y LIMITACIÓN DE RESPONSABILIDAD</h4>
                        <p>El único recurso del Vendedor en caso de que THWORK3000,S.L.   incumpla esta Orden de compra será el derecho a indemnización por daños, en una cuantía igual a la diferencia entre el precio de mercado de los Materiales o Servicios correspondientes en el momento de dicho incumplimiento y el precio de compra especificado en esta Orden de compra. THWORK3000,S.L.   NO SERÁ EN NINGÚN CASO RESPONSABLE ANTE EL VENDEDOR, NI ANTE NINGÚN TERCERO, POR DAÑOS FORTUITOS, INDIRECTOS, ESPECIALES O EMERGENTES CAUSADOS POR ESTA ORDEN DE COMPRA, O EN RELACIÓN CON ELLA, INDEPENDIENTEMENTE DE SI EL VENDEDOR O LA TERCERA PARTE FUERON INFORMADOS O NO DE LA POSIBILIDAD DE DICHOS DAÑOS.</p>

                        <h4>6. VENDEDOR INDEPENDIENTE</h4>
                        <p>El Vendedor es un proveedor independiente a todos los efectos, sin ninguna autoridad expresa o implícita para vincular a THWORK3000,S.L.   por contrato o de cualquier otro modo. El Vendedor garantizará (a su coste exclusivo) el seguro de Indemnización a Trabajadores, los seguros por incapacidad y cualquier otro seguro requerido por ley. THWORK3000,S.L.   no proporcionará ningún tipo de prestaciones para el Vendedor ni sus empleados, ni tendrá responsabilidad alguna de pagar por ello. El Vendedor pagará todos los impuestos necesarios, ya sean de naturaleza local, regional o estatal. Esto incluye, entre otras cosas, los impuestos sobre la renta, los pagos a la Seguridad Social, los impuestos sobre las nóminas o las tasas de trabajadores autónomos, los impuestos de compensación por desempleo y cualquier otra tasa, cargo, licencia o pago exigido por la ley sobre cualquier remuneración que el Vendedor reciba de THWORK3000,S.L.   de conformidad con este acuerdo. Por el presente documento, el Vendedor renuncia a cualquier derecho de reclamación o medida contra THWORK3000,S.L.   o alguno de sus afiliados en lo que respecta a prestaciones para empleados durante el periodo de trabajo abarcado en este acuerdo.</p>

                        <h4>7. CUMPLIMIENTO DE LA LEY</h4>
                        <p>El Vendedor cumplirá con toda la legislación, las normativas y los reglamentos aplicables en lo que se refiere a su comportamiento en virtud de este documento. A petición de THWORK3000,S.L.  , el Vendedor emitirá certificados que demuestren el cumplimiento de cualquier ley o normativa que pueda ser aplicable a los Materiales o Servicios incluidos en esta Orden de compra y, en cada caso, en la forma y el contenido que sean satisfactorios para THWORK3000,S.L.</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>8. CONTROLES DE IMPORTACIÓN/EXPORTACIÓN</h4>
                        <p>El Vendedor no usará, exportará, reexportará ni divulgará de ningún otro modo ningún tipo de tecnología o datos técnicos que THWORK3000,S.L.   haya proporcionado al Vendedor ni a cualquiera de sus empresas controladas o afiliadas al amparo de esta Orden de compra, a no ser que ello sea necesario para cumplir con todas las leyes y normativas aplicables en materia de exportación en los EE. UU.</p>
                        <p>El Vendedor acepta —a no ser que se acuerde lo contrario de manera expresa— que THWORK3000,S.L.   no sea una parte de las importaciones extranjeras de los Materiales, que las transacciones establecidas en esta Orden de compra se realicen después de la importación, que el Vendedor no causará ni permitirá que el nombre de THWORK3000,S.L.   aparezca como "importador nominal" en ninguna declaración de aduana y que el Vendedor cumplirá con todas las leyes, normas y regulaciones aplicables con respecto a la importación y las compras al extranjero.</p>


                        <h4>9. GRAVÁMENES Y RECLAMACIONES</h4>
                        <p>El Vendedor indemnizará por completo a THWORK3000,S.L.   y al propietario ante cualquier gravamen y/o reclamación que operarios, proveedores de materiales y subcontratistas del Vendedor puedan realizar contra THWORK3000,S.L.   o la propiedad en la que se realizan los Servicios, o para la que estos se llevan a cabo, y proporcionará a THWORK3000,S.L.  , si este lo solicita, declaraciones juradas de estados de cuentas y descargos de gravámenes.</p>

                        <h4>10.	PROPIEDAD DE THWORK3000,S.L.</h4>
                        <p>Todos los materiales y el inventario proporcionados por THWORK3000,S.L.  , o para los que THWORK3000,S.L.   haya autorizado al Vendedor a adquirir, desarrollar o diseñar a fin de trabajar en esta Orden de compra, serán propiedad exclusiva de THWORK3000,S.L.  . La propiedad deberá estar catalogada y mantenerse en condiciones adecuadas para la realización del trabajo (a cargo del Vendedor) y será devuelta a THWORK3000,S.L.   en un plazo de 72 horas tras la rescisión, el vencimiento o la petición de THWORK3000,S.L.  . La entrega de la propiedad se realizará en la planta del Vendedor (FOB). La propiedad se mantendrá a riesgo del Vendedor. Todos los costes por materiales e inventario están incluidos en el precio de esta Orden de compra.</p>

                        <h4>11.	RETIRADA DEL PRODUCTO</h4>
                        <p>Si el Vendedor, el comprador, alguna agencia estatal o algún tribunal determina que alguno de los materiales vendidos al comprador en virtud de este documento contiene algún defecto de calidad o deficiencia de rendimiento o que no cumple con normativas o requisitos y, por tanto, es aconsejable reprocesarlo o retirarlo, el Vendedor o el comprador compartirá de inmediato con la otra parte todos los datos relevantes. Así mismo, tomará las medidas correctoras necesarias, siempre y cuando el comprador coopere con el Vendedor en cualquier medida correctora o cualquier presentación de documentos que sea necesaria y siempre y cuando no haya nada en esta sección que impida que el comprador tome dicha acción, según sea necesario por leyes o normativas. El Vendedor pagará todos los gastos razonables empleados para determinar si es necesaria una reelaboración o retirada y realizará todas las reparaciones o modificaciones necesarias a su cargo exclusivo, a no ser que el Vendedor y el comprador hayan acordado, con base en otras condiciones aceptadas mutuamente, que dichas reparaciones las realice el comprador. Las partes reconocen que es posible que haya otros productos fabricados por el Vendedor o el comprador que contengan el mismo defecto o la misma condición de no cumplimiento. El comprador y el Vendedor aceptan que cualquier retirada de materiales del comprador se tratará de manera diferente e independiente en comparación con retiradas similares de productos del Vendedor, siempre y cuando dicho tratamiento diferente e independiente sea legal y el Vendedor siempre proporcione a los materiales del comprador un nivel de protección igual o mayor que el que ofrece a sus otros clientes en situaciones de retiradas similares. Cada parte consultará a la otra antes de hacer cualquier declaración pública, o ante alguna agencia estatal, sobre los peligros de seguridad potenciales de los materiales comprados en virtud de este documento, a no ser que dicha consulta imposibilite el cumplimiento de los plazos de notificación exigidos por la ley.</p>

                        <h4>12.	OBSOLESCENCIA</h4>
                        <p>El Vendedor accede a notificar a THWORK3000,S.L.   dentro de un periodo de tiempo razonable y en un momento que permita mantener el interés de THWORK3000,S.L.   en la obsolescencia sospechada o conocida del producto que afecta a los Materiales del presente acuerdo. El vendedor hará lo posible por realizar un seguimiento de la disponibilidad comercial de las piezas relacionadas con los Materiales;</p>
                        <p>Obtener compras de última hora en la cantidad necesaria para satisfacer las demandas de THWORK3000,S.L., y</p>
                        <p>encontrar alternativas adecuadas para las piezas de las que se conoce o se sospecha su obsolescencia durante un periodo de cinco (5) años o durante la duración del contrato (el más largo de ambos periodos).</p>
                    </td>
                </tr>
                <tr>
                    <td>


                        <h4>13.	MINERALES DE SANGRE</h4>
                        <p>El Vendedor acepta y declara que responderá de manera puntual a cualquier solicitud de información realizada por THWORK3000,S.L.  , o en su nombre, con respecto a información sobre Materiales entregados en virtud de este documento que contengan Minerales de Sangre ("CM", por las siglas de su traducción al inglés), según se definen en la sección 13(p) de la Ley del Mercado de Valores de 1934 y la sección 1502 de la Ley Dodd-Frank para la Reforma de Wall Street y para la Protección al Consumidor (en conjunto, la Normativa sobre Minerales de Sangre"). Dicha información deberá ser</p>
                        <ul>
                            <li>1. Una declaración realizada por el Vendedor, a su mejor saber y entender, tras la diligencia debida adecuada, y</li>
                            <li>2. Enviada a THWORK3000,S.L. o a un tercero autorizado, de la manera solicitada por THWORK3000,S.L. , en un periodo no superior a quince (15) días laborales desde la recepción de la solicitud de THWORK3000,S.L.</li>
                        </ul>




                    </td>
                </tr>
                <tr>
                    <td>
                    <p>El Vendedor incluirá el contenido principal de esta sección sobre minerales de sangre en todos los acuerdos de subcontratación otorgados por el Vendedor para la realización de los trabajos abarcados en este acuerdo. El Vendedor también acepta y declara que notificará a THWORK3000,S.L.   en caso de que hubiese cambios en la información provista en virtud de esta cláusula y que proporcionará cualquier otra información que THWORK3000,S.L.   solicite para garantizar el cumplimiento de la Normativa sobre Minerales de Sangre. El incumplimiento de las obligaciones del Vendedor en virtud de esta cláusula será considerado un incumplimiento grave de estas condiciones.</p>
                    <h4>14.	CLÁUSULA DE AUDITORÍA PARA COSTES MÁS CANTIDAD ADICIONAL U ÓRDENES DE COMPRA POR CATÁLOGO</h4>
                        <p>THWORK3000,S.L.   puede auditar las órdenes de compra realizadas para garantizar que los precios se aplican de manera uniforme en todas las órdenes de compra. Si hay una auditoría, THWORK3000,S.L.   realizará un muestreo estadístico de todas las Órdenes de compra ("Muestreo") efectuadas a través del Vendedor. El porcentaje de error del Muestreo entre el precio real pagado y el precio contratado se aplicará al valor total de compra de todas las órdenes de compra realizadas durante el plazo del acuerdo. La parte que se haya beneficiado del error pagará la diferencia a la otra parte en un plazo de treinta (30) días desde la finalización de la auditoría. El Vendedor recibirá los detalles del análisis. El Vendedor accede a cooperar plenamente con THWORK3000,S.L.   durante la auditoría y a proporcionarle todos los documentos pertinentes para realizarla.</p>

                        <h4>15.	OBLIGACIONES PARA MÉTODOS DE SOLICITUD ESPECIALES</h4>
                        <p>Los productos comprados al amparo del calendario de entregas acordado pueden cambiar a lo largo de la duración del contrato, ya que las circunstancias van cambiando. Las partes acuerdan y entienden que las cantidades previstas son solo estimaciones y no implican un compromiso firme en favor de THWORK3000,S.L.  . Las órdenes marco o generales están basadas en el valor en dólares estimado del gasto de THWORK3000,S.L.   ("Cantidades Estimadas"). THWORK3000,S.L.   no está obligada a comprar o cubrir las Cantidades Estimadas. El Vendedor debe notificar a THWORK3000,S.L.   por escrito sobre cualquier excedente potencial o real de las Cantidades Estimadas, a fin de obtener la autorización y aprobación adecuadas para el aumento en la Cantidad Estimada. THWORK3000,S.L.   no está obligada a pagar por Materiales o Servicios si los costes superan las Cantidades Estimadas.</p>


                        <h4>16.	DIVISIBILIDAD</h4>
                        <p>El hecho de que alguna disposición en esta Orden de compra se declare no válida, ilegal o inaplicable, no afectará ni perjudicará en modo alguno a la validez, legalidad y aplicabilidad del resto de disposiciones.</p>

                        <h4>17.	ASIGNACIÓN, SIN RENUNCIA</h4>
                        <p>El Vendedor no podrá asignar esta Orden de compra ni ningunos de sus derechos u obligaciones en virtud de este documento sin el consentimiento previo de THWORK3000,S.L.   y cualquier asignación realizada sin dicho consentimiento será considerada nula. Ninguna renuncia por incumplimiento, en virtud de este documento, de cualquier término o condición de la Orden de compra será considerada como una renuncia por cualquier otro incumplimiento o a cualquier otro término o condición.</p>

                        <h4>18.	AVISOS</h4>
                        <p>Todos los avisos y demás comunicados relacionados con esta Orden de compra, incluyendo los consentimientos, se harán por escrito y deben dirigirse al Vendedor o a THWORK3000,S.L.   en las direcciones detalladas en el anverso de esta Orden de compra, y se considerarán entregadas en los siguientes casos:</p>
                        <ul>
                            <li>a. Si se entregan personalmente.</li>
                            <li>b. Si se envían por fax o correo electrónico confirmado.</li>
                            <li>c. Si se envían por mensajería rápida comercial con acuse de recibo por escrito.</li>
                            <li>d. Pasados tres (3) días tras el envío con franqueo pagado y con envío prioritario o certificado.</li>
                        </ul>

                        <h4>1.	SUBSISTENCIA DE LAS OBLIGACIONES</h4>
                        <p>Cualquier obligación o deber que por su naturaleza se extienda más allá del vencimiento o la rescisión de esta Orden de compra permanecerá vigente tras dicho vencimiento o rescisión.</p>

                        <h4>2.	LEGISLACIÓN APLICABLE. Para las Órdenes de compra realizadas dentro de los Estados Unidos:</h4>
                        <p>Esta Orden de compra se regirá e interpretará de conformidad con las leyes de ESPAÑA, sometiéndose a los tribunales de Algeciras, Cádiz.</p>


                    </td>
                </tr>

                <tr>
                    <td>
                    <h4>3.	GRATIFICACIONES</h4>
                        <p>El Vendedor garantiza que ni él ni ninguno de sus empleados, agentes o representantes han ofrecido o dado ninguna gratificación al comprador, y que tampoco lo harán en el futuro</p>

                        <h4>4.	INTEGRIDAD DEL ACUERDO, MODIFICACIONES</h4>
                        <p>Esta Orden de compra es la declaración final y completa de las condiciones del contrato entre las partes y sustituye a cualquier otro acuerdo o negociación anterior o actual, ya sea oral o escrita, en relación con su objeto. Esta Orden de compra solo puede ser modificada por escrito y con la firma de ambas partes.</p>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
