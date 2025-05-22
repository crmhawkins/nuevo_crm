@extends('layouts.adminBudget')

@section('content')

@section('css')
    <style>
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .activida .window-module>span{
            margin-left: 2rem;
            }/*"tiempo estimado:"*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .js-react-root .window-module{
            margin-bottom: 0;
            }/*h3 descripción*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .js-react-root .u-gutter{
            margin-top:0;
            }/*"añadir una descripción mas detallada..."*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .header-modal .nch-button  {
            display: table;
            margin: 1rem 2rem;
            }/*botón:"ir al presupuesto"*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .js-react-root .u-gutter .editable .description-content #formDes .description-edit{
            margin: 0.5rem 0;
            }/*"añadir una descripción mas detallada..."*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container #budgetUl .drag-column .drag-inner-list .drag-item p{
            margin-bottom: 0.5rem !important;
            }/*li*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div.js-react-root .notas{
            margin: 0 2rem;
            }/*notas puestas bajo el botón "guardar"*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .activida .window-module .tareas-trello .drag-inner-list .descrip-tarea-trello .btn{
            margin-top: 1rem;
            }/*botón: "escribir comentario"*/
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .main-modal .js-fill-card-detail-desc div .js-react-root .notas{
                margin-left: 3rem; /*descripciones guardadas*/
            }
            .fixed-top-nav #page_cont .contenedorGral {  /* div general*/
                max-height: 3rem;
                background-color: #37379e; /*#343d46*/
                displaY: flex;
                padding: 0.25rem;
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorLogo{ /*sub div: contenedorLogo*/
                margin: auto 3rem;
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorInput{ /*sub div: contenedorInput*/
                display: flex;
                overflow: hidden;
                margin-left: auto;
                margin-right: 10rem;
                padding: 0 0;
                background: #2b303b;
                border: 1px solid #393939;
                border-radius: 0.25rem;
                font-size: 1rem;
                width: 14rem;

                white-space: nowrap;
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorInput input{   /*input para buscar presupuestos*/
                border: none;
                background-color:rgba(0, 0, 0, 0);
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorInput:hover, .fixed-top-nav #page_cont .header .contenedorInput:focus, .fixed-top-nav #page_cont .header .contenedorInput:active{ /*hover sobre sub div-contenedorInput*/
                outline:none;
                background: #ffffff;
                -webkit-transition: background .55s ease;
                -moz-transition: background .55s ease;
                -ms-transition: background .55s ease;
                -o-transition: background .55s ease;
                transition: background .55s ease;
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorInput .icon{   /*icono lupa*/
                z-index: 1;
                color: #4f5b66;
                padding: 0.5rem 0.8rem;
            }
            .fixed-top-nav #page_cont .contenedorGral .contenedorLogo .claseInicio {   /* enlace <a> a:"CRM Hawkins"*/
                color: #fff;
                font-weight: 400;
                text-decoration: none;
            }
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content{ /*modalPadre*/
                display: block;
                width: 768px;
                overflow:hidden;
                padding: 8px;
            }
            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-main-col{   /*modalPadre/columnaCentral*/
                float: left;
                margin: 0;
                min-height: 24px;
                padding: 8px;
                position: relative;
                width: 552px;
                z-index: 1;
            }

            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-sidebar{    /*modalPadre/sidebar*/
                float: right;
                overflow: hidden;
                padding: 68px 8px 8px 8px;
                width: calc(100% - 565px);
                z-index: 10;
            }

            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-sidebar span{  /*modalPadre/sidebar/span "Añadir al presupuesto"*/
                margin-left:0;
                font-weight: bold;
            }

            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-sidebar .u-clearfix{    /*modalPadre/sidebar/div*/
                display: block;
            }

            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-sidebar .u-clearfix .btn-primary{   /*modalPadre/sidebar/div/botones*/
                background-color: var(--ds-background-neutral,#091e420a);
                border: none;
                border-radius: 3px;
                box-shadow: none;
                box-sizing: border-box;
                color: var(--ds-text,inherit);
                cursor: pointer;
                display: block;
                height: 32px;
                margin-top: 8px;
                max-width: 300px;
                overflow: hidden;
                padding: 6px 12px;
                position: relative;
                text-decoration: none;
                text-overflow: ellipsis;
                transition-duration: 85ms;
                transition-property: background-color,border-color,box-shadow;
                transition-timing-function: ease;
                -webkit-user-select: none;
                user-select: none;
                white-space: nowrap;
            }


            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .window-sidebar .u-clearfix .btn-primary .js-sidebar-action-text{   /*modalPadre/sidebar/div/botones/texto dentro del span*/
                font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Noto Sans,Ubuntu,Droid Sans,Helvetica Neue,sans-serif;
                font-size: 14px;
                font-weight: 400;
                line-height: 20px;
                margin-left: 6px;
            }


            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .modal-body form .listaPadre .listaHijo label .contenedorColor .bandaColor{    /*modalPadre/sidebar/etiquetas/modalHijoEtiquetas/bandasDeColores*/
                width: 23rem;
                height: 2rem;
                border-radius: 0.2rem;
                opacity: 0.5;
            }

            .fixed-top-nav #page_cont .container-fluid .row .col-12 .drag-container .drag-list .drag-column .drag-inner-list .modal .modal-dialog .modal-content .modal-body form .listaPadre .listaHijo label .contenedorColor .bandaColor: hover{    /*modalPadre/sidebar/etiquetas/modalHijoEtiquetas/bandasDeColores*/
                opacity: 1;
            }



    </style>

@endsection
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">


<div class=contenedorGral>  <!--contenedor general de formulario-->
    <div class=contenedorLogo> <!--contenedor botón inicio-->
        <a class="claseInicio" href="https://crmhawkins.com/admin/dashboard">  <!--botón inicio-->
            <i class="fab fa-trello"></i>
            CRM Hawkins
        </a>
    </div>
    <div class=contenedorInput> <!--contenedor formulario--->
            <span class="icon"><i class="fa fa-search"></i></span>
            <input type="search" id="query" name="q" placeholder="Busca aquí..." aria-label="Busca en el contenido"> <!--formulario-->
    </div>
</div>





<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
{{-- <script src="https://code.jquery.com/jquery-3.6.0.slim.js" integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY=" crossorigin="anonymous"></script> --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="header_trello">

</div>
<div id="content" class="container-fluid trello_column" style="padding-top: 0 !important; background: url(/images/backgroundTrello.png); background-repeat: no-repeat; background-size: cover;">

    <div class="row">
        <div class="col-12">
            <div class="drag-container">
                <?php $countOrder = 1 ?>
                @if($budgetsAccept)
                @foreach($budgetsAccept as $budget)
                <ul id="budgetUl" class="drag-list" draggable="true" data-order="" data-target="{{$countOrder}}" data-id="{{$budget->id}}">
                    <li class="drag-column drag-column-in-progress" data-id="{{$budget->id}}" data-target="{{$budget->id}}">
                        <span class="drag-column-header">
                            @if($budget->client)
                            <h2>{{$budget->client->name}}</h2>
                            @endif
                            <svg class="drag-header-more" data-target="options1" fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24" style="fill:black;">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></svg>
                        </span>
                        @if($budget->budget_status_id == 1)

                        <p>
                            <span style="width: 30px;
                                background: orange;
                                height: 8px;
                                border-radius: 10px;
                                margin-right: 0.4rem;
                                margin-top: 0.2rem;
                                display: inline-block;"> Pendiente de confirmar

                            </span>
                        </p>

                        @endif

                        @if($budget->budget_status_id == 2)
                        <p>
                            <span style="width: 30px;
                                background: red;
                                height: 8px;
                                border-radius: 10px;
                                margin-right: 0.4rem;
                                margin-top: 0.2rem;
                                display: inline-block;">

                            </span>
                            <span style="font-size: 12px">Pendiente de aceptar</span>
                        </p>
                        @endif

                        @if($budget->budget_status_id == 3)
                        <p>
                            <span style="width: 30px;
                                background: green;
                                height: 8px;
                                border-radius: 10px;
                                margin-right: 0.4rem;
                                margin-top: 0.2rem;
                                display: inline-block;">

                            </span>
                            <span style="font-size: 12px">Aceptado</span>
                        </p>
                        @endif

                        @if($budget->budget_status_id == 6)
                        <p>
                            <span style="width: 30px;
                                background: blue;
                                height: 8px;
                                border-radius: 10px;
                                margin-right: 0.4rem;
                                margin-top: 0.2rem;
                                display: inline-block;">

                            </span>
                            <span style="font-size: 12px">Facturado</span>
                        </p>
                        @endif

                        @if($budget->budget_status_id == 7)
                        <p>
                            <span style="width: 30px;
                                background: black;
                                height: 8px;
                                border-radius: 10px;
                                margin-right: 0.4rem;
                                margin-top: 0.2rem;
                                display: inline-block;">

                            </span>
                            <span style="font-size: 12px">Facturado parcial</span>
                        </p>
                        @endif
                        <ul class="drag-inner-list" id="1" style="padding-left: 0;">
                            <?php $budgetID = $budget->id ?>
                            <li class="drag-item" data-toggle="modal" data-id="{{$budget->id}}" data-target="#{{$budget->id}}">
                                <p class="hide">{{$budget->reference}}</p>

                                @if($budget->client)
                                <p>{{$budget->client->name}}</p>
                                @endif
                                <p class="hide">{{$budget->project->name}}</p>
                                <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                            Launch demo modal
                                        </button> -->
                            </li>

                            <div class="modal fade {{$budget->id}}" id="{{$budget->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content modalContenido">   <!--modalPadre-->

                                        <div class="window-sidebar">    <!--modalPadre/sidebar: inicio-->
                                            <span class="trello-item-subtitulo">Añadir al presupuesto</span>
                                            <div class="u-clearfix">

                                                <!-- BOTÓN DEL MODAL. Copiado de https://getbootstrap.com/docs/4.0/components/modal/#varying-modal-content SUBTIPO: Varying modal content -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">
                                                    <span class="js-sidebar-action-text">
                                                        <i class="fab fa-trello"></i>
                                                        Etiquetas
                                                    </span>
                                                </button>


                                            </div>
                                        </div> <!--sidebar: fin-->
                                        <div class="window-main-col">   <!--modalPadre/columna central: inicio-->
                                            <div class="header-modal">
                                                <span class="header-title-modal"><i class="fa-solid fa-address-card"></i> <span style="margin-left:0.6rem;">{{$budget->client['name']}}</span></span>
                                                <span class="trello-item-subtitulo">en la lista @if($budget->budget_status_id == 1)Pendiente de confirmar @elseif($budget->budget_status_id == 2) Pendiente de aceptar @endif</span>

                                                <!--xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxAQUÍ INSERTO LAS ETIQUETAS EN EL MODAL PAFDRExxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-->
                                                coloresSeleccionados();




                                                <p class="header-title-modal" style="font-size:0.85rem !important; margin-left: 34px; margin-top: 10px">Creado el dia: {{$budget->created_at}}</p>
                                                <a href="/admin/budgets/{{$budget->id}}/edit" target="_blank" class="nch-button nch-button--primary confirm mod-submit-edit js-save-edit"> Ir al Presupuesto</a>
                                            </div>

                                            <div class="main-modal">
                                                <div class="js-fill-card-detail-desc">
                                                    <div>
                                                        <div class="js-react-root">
                                                            <div class="window-module">
                                                                <div class="window-module-title window-module-title-no-divider description-title">
                                                                    <span class="icon-description icon-lg window-module-title-icon">
                                                                        <i class="fa-solid fa-align-left"></i>
                                                                    </span>
                                                                    <h3 class="u-inline-block">Descripción</h3>

                                                                </div>
                                                            </div>
                                                            <!-- aquí iba -->
                                                            <div class="u-gutter">
                                                                <div attr="desc" class="editable">
                                                                    <div class="description-content js-desc-content">
                                                                        <div class="description-content-fade-button">
                                                                            <!-- <span class="description-content-fade-button-text">Mostrar descripción completa.</span> -->
                                                                        </div>
                                                                        <form id="formDes" action="" data-id="{{$budget->id}}">
                                                                            <div dir="auto" class="current markeddown hide-on-edit js-desc js-show-with-desc hide"></div>
                                                                            <!--  <p class="u-bottom js-hide-with-desc">
                                                                                    <a href="#" class="description-fake-text-area hide-on-edit js-edit-desc js-hide-with-draft">Añadir una descripción más detallada...</a>
                                                                                </p> -->
                                                                            <div id="textAreaDescripcion" class="description-edit edit">
                                                                                <!-- <a href="#" class="helper nch-button js-format-help">Guía de Formato</a> -->
                                                                                <textarea placeholder="Añadir una descripción más detallada..." class="field field-autosave js-description-draft description card-description"></textarea>
                                                                            </div>
                                                                            <div class="edit-controls u-clearfix">
                                                                                <input id="buttonDescripcion" class="nch-button nch-button--primary confirm mod-submit-edit js-save-edit" type="submit" value="Guardar">
                                                                                <a class="icon-lg icon-close dark-hover cancel js-cancel-edit" href="#"></a>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="notas">
                                                                @if($budget['budgetDescripcion'] != '')
                                                                @foreach($budget['budgetDescripcion'] as $item)
                                                                <p class="nota">
                                                                    {{$item->descripcion}}
                                                                </p>
                                                                @endforeach
                                                                @endif
                                                            </div>

                                                        </div>

                                                        <div class="activida"> <!--modalPadre/ "Tareas": inicio-->
                                                            <div class="window-module">
                                                                <div class="window-module-title window-module-title-no-divider description-title">
                                                                    <span class="icon-description icon-lg window-module-title-icon">
                                                                        <i class="fa-solid fa-bars-progress"></i>
                                                                    </span>
                                                                    <h3 class="u-inline-block">Tareas</h3>
                                                                </div>
                                                                <?php
                                                                $diasTotal = 0;
                                                                $horasTotal = 0;
                                                                $minutosTotal = 0;
                                                                $segundosTotal = 0;

                                                                if ($budgetsAccept) {
                                                                    foreach ($budgetsAccept as $budget) {
                                                                        if ($budget['id'] == $budgetID) {
                                                                            if ($budget['task']) {
                                                                                foreach ($budget['task'] as $task) {
                                                                                    if ($task) {
                                                                                        $tiempoTotal = explode(':', $task->estimated_time);
                                                                                        $segundosTotal += $tiempoTotal[2];
                                                                                        if ($segundosTotal >= 60) {
                                                                                            $segundosTotal -= 60;
                                                                                            $minutosTotal += 1;
                                                                                        }
                                                                                        $minutosTotal += $tiempoTotal[1];
                                                                                        if ($minutosTotal >= 60) {
                                                                                            $minutosTotal -= 60;
                                                                                            $horasTotal += 1;
                                                                                        }
                                                                                        $horasTotal += $tiempoTotal[0];
                                                                                        if ($horasTotal >= 24) {
                                                                                            $horasTotal -= 24;
                                                                                            $diasTotal += 1;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                ?>
                                                                <span>@if($diasTotal > 0)Dias: {{$diasTotal}} + @endif Tiempo Estimado: <?php

                                                                                                                                        $numeroConCeros3 = str_pad($horasTotal, 2, "0", STR_PAD_LEFT);

                                                                                                                                        echo $numeroConCeros3; ?>:<?php

                                                                                                                                                                    $numeroConCeros2 = str_pad($minutosTotal, 2, "0", STR_PAD_LEFT);

                                                                                                                                                                    echo $numeroConCeros2; ?>:<?php

                                                                                                                                                                                                $numeroConCeros = str_pad($segundosTotal, 2, "0", STR_PAD_LEFT);

                                                                                                                                                                                                echo $numeroConCeros; ?></span>
                                                                <div class="tareas-trello">
                                                                    <ul class="drag-inner-list" id="9">
                                                                        @if($budgetsAccept)
                                                                        @foreach($budgetsAccept as $budget)
                                                                        <?php
                                                                        if ($budget['id'] == $budgetID) {
                                                                            // //var_dump($budget['task']);
                                                                            // var_dump($budget['metas']);
                                                                            // var_dump($budgetID);
                                                                        ?>
                                                                            @if($budget['task'])
                                                                            @foreach($budget['task'] as $task)

                                                                            <li class="drag-item tarea-drag-item" data-toggle="collapse" href="#collapseExample{{$task->id}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                                                <p class="">{{$task->title}}</p>
                                                                                <p class="">Compañero Asignado: <?php
                                                                                                                $user = DB::table('admin_user')->where('id', $task->admin_user_id)->first();
                                                                                                                $us = json_decode(json_encode($user), true);
                                                                                                                //$result = json_decode($user, true);
                                                                                                                //var_dump(json_decode(json_encode($user)));
                                                                                                                echo $us["name"];
                                                                                                                ?>
                                                                                </p>
                                                                                <p>Tiempo estimado: {{$task->estimated_time}}<span> - Tiempo Consumido: {{$task->real_time}}</span>
                                                                                </p>


                                                                            </li>
                                                                            <div class="descrip-tarea-trello bd-example-modal{{$task->id}} collapse" id="collapseExample{{$task->id}}">

                                                                                @if(count($task['metas']) > 0)
                                                                                @foreach($task['metas'] as $meta)
                                                                                <div class="nota">

                                                                                    <span class="autor_tarea_meta">{{$task['usuario']->name}}</span>
                                                                                    <?php echo (htmlspecialchars_decode($meta->description, ENT_QUOTES)); ?>

                                                                                    <p class="datos_metas_info">{{$meta->created_at}}</p>

                                                                                </div>

                                                                                @endforeach
                                                                                @else
                                                                                <p>No hay comentarios en esta tarea.</p>
                                                                                @endif

                                                                                <button data-toggle="modal" data-target="#test2" onclick="mostrarCajaComentarios(<?php echo ($task->id) ?>,<?php echo ($task->usuario->id) ?>)" id="escribirComentariow" class="btn btn-dark">Escribir Comentario</button>
                                                                            </div>

                                                                            @endforeach
                                                                            @endif
                                                                        <?php } ?>

                                                                        @endforeach
                                                                        @endif

                                                                    </ul>
                                                                </div>


                                                            </div>

                                                        </div>  <!--"Tareas": fin-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!--columnaCentral: fin-->

                                    </div>
                                </div>
                            </div>

                            <!--modalHijoEtiquetas html-->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                      <form>
                                        <div class="form-group">
                                          <label for="recipient-name" class="col-form-label">Recipient:</label>
                                          <input type="text" class="form-control" id="recipient-name">
                                        </div>
                                        <span class="trello-item-subtitulo">Etiquetas</span>
                                            <form method="POST" action="?">
                                                <ul class="listaPadre">
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="verde" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #5AAC44; "><input name="verdeText" type="text" style=" background:transparent; border:none; padding-top=0.1rem;"></div>

                                                            </div>
                                                        </label>
                                                    </li>
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="amarillo" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #E6C60D; "></div>
                                                            </div>
                                                        </label>
                                                    </li>
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="naranja" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #E79217; "></div>
                                                            </div>
                                                        </label>
                                                    </li>
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="rojo" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #CF513D; "></div>
                                                            </div>
                                                        </label>
                                                    </li>
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="morado" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #A86CC1; "></div>
                                                            </div>
                                                        </label>
                                                    </li>
                                                    <li class="listaHijo">
                                                        <label style=" display:flex;">
                                                            <div class="contenedorCheckbox" style="margin: 0.25rem 0.75rem">
                                                                <input name="azul" class="checkbox" type="checkbox" aria-checked="true" aria-disabled="false" aria-invalid="false" value onchange="this.form.submit()">
                                                            </div>
                                                            <div class="contenedorColor">
                                                                    <div class="bandaColor" style="background-color: #0079BF; "></div>
                                                            </div>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </form>

                                            <?php
                                            if(isset($_POST['submit'])){
                                                if(!empty($_POST['colores'])){
                                                    foreach($_POST['colores'] as $value){
                                                        echo "value : ".$value.'<br/>';
                                                    }
                                                }
                                            }
                                            ?>

                                            /*aquiiiiiiiiiiiiiiiiiiii*/

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                      <button type="button" class="btn btn-primary">Send message</button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </ul>
                    </li>
                </ul>
                </li>
                </ul>
                <?php $countOrder += 1 ?>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<div class="col-12 modal fade" id="test2" style="z-index: 1800;"></div>
<!-- <div class="container">
    <div draggable="true" class="box">A</div>
    <div draggable="true" class="box">B</div>
    <div draggable="true" class="box">C</div>
  </div> -->
<style>
    .drag-container {
        display: grid;
        gap: 10px;
        height: 95vh;
    }

    .drag-list {
        background-color: #ebecf0;
        border-radius: 0.4em;
        padding: 10px;
        cursor: move;
        order: 1;
        width: 272px;
        height: fit-content;

    }

    .drag-list.over {
        border: 3px dotted #666;
    }
</style>
@endsection

@section('scripts')
<!-- JS -->
<script type="text/javascript">
    function coloresSeleccionados(){  /*funcion para guardar colores seleccionados*/
    let coloresSeleccionados = document.querySelector('input[name="colores"]:checked').value;
    console.log(activoFijo);
    }
</script>

<script src="https://cdn.ckeditor.com/4.18.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        descripcionTrollo()

    });
</script>
<script type="text/javascript">
    function mostrarCajaComentarios(dataId, dataUser) {
        var divNodo = document.querySelector('#test2')
        if (!divNodo.firstChild) {

            var data = `<div class="modal-dialog modal-xl" id="modalTextArea">
                <div class="modal-content">
                    <div class="modal-body">
                        <form id="form-comentarios" method="post">
                        <input type="hidden" name="gestor" value="${dataUser}">
                        <input type="hidden" name="tareaId" value="${dataId}">
                        <input type="hidden" name="user" value="${dataUser}">
                        <textarea id="editor" class="input-mensaje" name="descripcion${dataId}" placeholder="Escriba su mensaje..."></textarea>
                        <input id="enviar" class="boton-enviar" data-id="${dataId}" type="submit" value="Enviar">
                        </form>
                    </div>
                </div>
                </div>
                `;
            let contenedor = document.querySelector('#test2');
            let p = document.createElement("div");
            p.innerHTML = data;
            contenedor.appendChild(p);
            crearEditor(editor);


        }


        const botonEnviarComentario = document.querySelector('.boton-enviar');
        botonEnviarComentario.addEventListener('click', function(e) {
            e.preventDefault();

            var idButton = $(this).attr('data-id');
            // console.log(idButton)
            var dataSend = CKEDITOR.instances.editor.getData();

            $.when(setNuevaNota(dataUser, dataId, dataSend, dataUser)).then(function(data) {

                if (data.estado == "OK") {
                    $('#test2').toggle();
                    swal(
                        'Éxito',
                        'Su nota se ha registrado correctamente',
                        'success',
                    )

                    setInterval(() => {
                        location.reload();
                    }, 3000);


                } else {
                    // console.log(data)
                    swal(
                        'Error',
                        'Error la nota no se ha registrado.' + data.estado,
                        'error'
                    );
                }
            })
        })
    }

    function crearEditor(id) {
        CKEDITOR.replace(id, {
            filebrowserBrowseUrl: 'https://crmhawkins.com/ckfinder/ckfinder.html',
            filebrowserUploadUrl: 'https://crmhawkins.com/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
        });

        CKEDITOR.editorConfig = function(config) {
            config.language = 'es';
            config.uiColor = '#F7B42C';
            config.height = 300;
            config.toolbarCanCollapse = true;

        }

    }
</script>

<script type="text/javascript">
function descripcionTrollo() {
    var formu = document.querySelectorAll("#formDes");

    formu.forEach(form => {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            console.log(e.target[0].value);
            var id = $(this).attr('data-id');
            var dataSend = {};
            dataSend.id = id;
            dataSend.descripcion = e.target[0].value;
            console.log(dataSend);
            $.when(addDescripcion(dataSend)).then(function(response) {
                // swal(
                //         'Éxito',
                //         'Su nota se ha registrado correctamente',
                //         'success',
                //     )

                    // setInterval(() => {
                    //     location.reload();
                    // }, 5000);

            var inputBuscar = document.querySelector('input[name="q"]'); //inputBuscar guarda lo que se escribe en el input

             var envio = {
                target: {
                    value: inputBuscar.value,
                }
             }
             var modalId = $('#'+id)[0];
             console.log('modal: ',modalId);
             modalId.click()

            //  modalId.forEach(element => {
            //     element.modal('hide')
            //  });
                // $('#'+id).get(0).modal('hide')
            $.when(actualizarDom()).then(function(data) {
                console.log(data);
                var arrayPresupuesto = data;
                mostrarPresupuestos(envio, arrayPresupuesto)
            })

                // console.log('Response: ', response.estado);
                // if (response.estado == 'OK') {

                // }

            });
        });
    });
}
function htmlspecialchars_decode(string, quote_style) {
    // Convert special HTML entities back to characters
    //
    // version: 901.714
    // discuss at: http://phpjs.org/functions/htmlspecialchars_decode
    // +   original by: Mirek Slugen
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Mateusz "loonquawl" Zalega
    // +      input by: ReverseSyntax
    // +      input by: Slawomir Kaniecki
    // +      input by: Scott Cariss
    // +      input by: Francois
    // +   bugfixed by: Onno Marsman
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: get_html_translation_table
    // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');
    // *     returns 1: '<p>this -> &quot;</p>'
    var histogram = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();

    if (false === (histogram = get_html_translation_table('HTML_SPECIALCHARS', quote_style))) {
        return false;
    }

    // &amp; must be the last character when decoding!
    delete(histogram['&']);
    histogram['&'] = '&amp;';

    for (symbol in histogram) {
        entity = histogram[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }

    return tmp_str;
}

function templeNotas(array){
    let response = '';
    if(array.length>0){
        array.forEach(meta => {
            console.log(meta);
            response += `
            <div class="nota">
                    ${meta.descripcion}
            </div>
            `;
        })
    }else {
        response = `<div class="nota"><p>No hay comentarios</p></div>`;
    }

    return response;
}



function templeMeta(array){
    let response = '';
    if(array.length>0){
        array.forEach(meta => {
            console.log(meta);
            response += `
            <div class="nota">
                <span class="autor_tarea_meta">${meta.usuario.name}</span>
                    ${$("<div/>").html(meta.description).text()}
                <p class="datos_metas_info">${meta.created_at}</p>
            </div>
            `;
        })
    }else {
        response = `<p>No hay comentarios</p>`;
    }

    return response;
}


function tempolateastatus2(tareaSingle){
    let response = '';
    if(tareaSingle.length>0){

        tareaSingle.forEach(tarea => {
            response += `
                <li class="drag-item tarea-drag-item" data-toggle="collapse" href="#collapseExample${tarea.id}" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <p class="">${tarea.title}</p>
                    <p class="">Compañero Asignado:</p>
                    <p>Tiempo estimado: ${tarea.estimated_time}<span> - Tiempo Consumido: ${tarea.real_time}</span></p>
                </li>
                    <div class="descrip-tarea-trello bd-example-modal${tarea.id} collapse" id="collapseExample${tarea.id}">
                            ${templeMeta(tarea.metas)}
                    <button data-toggle="modal" data-target="#test2" onclick="mostrarCajaComentarios(${tarea.id}, 1)" id="escribirComentariow" class="btn btn-dark">Escribir Comentario</button>
                    </div>

            `;


        })
    } else {
        response = `<li><p>No hay tareas</p></li>`;
    }
    console.log(response);
    return response;
}

var inputBuscar = document.querySelector('input[name="q"]'); //inputBuscar guarda lo que se escribe en el input
console.log(inputBuscar);
var arrayPresupuesto = @json($budgetsAccept);   //paso la $variable de PHP a javaScript

inputBuscar.addEventListener('change', function(evento){   //cuando se cambia el input se lanza esta función. El parámetro evento=inputBuscar
    evento.preventDefault() //tomamos el control del error
    mostrarPresupuestos(evento, arrayPresupuesto );
})

    function mostrarPresupuestos(evento, arrayBudgets) {
        // var arrayPresupuesto = @json($budgetsAccept);
        var arrayPresupuesto = arrayBudgets;   //almacenamos en arrayPresupuesto todos los presupuetosAprobados
           //almacenamos en arrayPresupuesto todos los presupuetosAprobados
        var nuevoArray = [];    //colección que guarda los presupuestos cuyo clientName incluyen el input
        var divContainer = document.querySelector('.drag-container'); //divContainer guarda una parte del document que queremos imprimir
        var liID;
        divContainer.innerHTML = '';  //vacío el contenido del divContainer
        console.log(arrayPresupuesto);


        // evento.target.value buscar en el arrayPresupuesto en la columna title que contenga la palabra del input, lo que sean true añadir a un nuevo array
        arrayPresupuesto.forEach(function(presupuesto){  //recorro cada uno de los presupuestosAprobados, y uno a uno se le aplica esta función. El parámetro presupuesto=arrayPresupuesto
            var valueInput = evento.target.value.toUpperCase();         //guardo en valueInput el input.target.value.toUpperCase()
            var clientName = presupuesto.client.name.toUpperCase()      //guardo en clientName los presupuetosAprobados.client.name.toUpperCase()
            console.log(presupuesto.client.name);
            if(clientName.indexOf(valueInput)  >= 0){console.log('*el input ESTÁ CONTENIDO en el presupuesto.client.name');}
            console.log(clientName);

            if (clientName.includes(valueInput)) {
                nuevoArray.push(presupuesto); //si se cumple el if anterior, añado este presupusto a la colección nuevoArray
            }

        });


         /*if (evento.target.value.toUpperCase() == '') {

         }*/

        // imprimir en el divContainer el array nuevo con el mismo diseño anterior
        if(nuevoArray.length == 0){
            return divContainer.innerHTML = `<h2 style="color:white;margin: 0 auto;margin-top:3rem;">No se encontraron presupuesto con la palabra ${evento.target.value}</h2>`;
        }

        //si hay coincidencia en la busqueda, mostramos solo el presupuesto encontrado.
        var contador = 0

        console.log('*número de coincidencias= '+nuevoArray.length);


        if (evento.target.value.toUpperCase() == '') {

            for(i=0; i<arrayPresupuesto.length; i++){
                nuevoArray[i]= arrayPresupuesto[i];
                console.log('yo = '+nuevoArray[i].client['name']);
            }
        }

        nuevoArray.forEach(array => {  //recorro 'cada uno de los presupuestos cuyo clientName incluyen el input'=array

            var statusBudgets;
            var diasTotal = 0;
            var horasTotal = 0;
            var minutosTotal = 0;
            var segundosTotal = 0;
            var tiempoTotal = 0;
            console.log(array);


                if(array.task.length > 0) {             //si el presupuestoAprobado coindicente actual contiene una ó más tarea...
                    array.task.forEach(element => {     //recorro cada una de las tareas. 'Tarea actual'= element
                    //if ($task) {
                        tiempoTotal= element.estimated_time;     //tareaActual.tiempoEstimado==> lo paso a formato "hh/mm/ss". comienzo
                        segundosTotal=tiempoTotal[2];
                        if(segundosTotal >= 60) {
                            segundosTotal -= 60;
                            minutosTotal += 1;
                        }
                        minutosTotal += tiempoTotal[1];
                        if (minutosTotal >= 60) {
                            minutosTotal -= 60;
                            horasTotal += 1;
                        }
                        horasTotal += tiempoTotal[0];
                        if (horasTotal >= 24) {
                            horasTotal -= 24;
                            diasTotal += 1;
                        }
                    });
                }



                let imp;
            var numeroConCeros3 = horasTotal.toString().padStart(2, '0');

            var numeroConCeros2 = minutosTotal.toString().padStart(2, '0');

            var numeroConCeros = segundosTotal.toString().padStart(2, '0');  //fin. Imprimimos numeroConCerosN en el html como variables

            if (array.budget_status_id == 1) { statusBudgets = "Pendiente de confirmar" }
            if (array.budget_status_id == 2){ statusBudgets = "Pendiente de aceptar" }

            let tempolateastatus;

            if (array.budget_status_id == 1) {
                tempolateastatus = `
                <p>
                    <span style="width: 30px;
                        background: orange;
                        height: 8px;
                        border-radius: 10px;
                        margin-right: 0.4rem;
                        margin-top: 0.2rem;
                        display: inline-block;"> Pendiente de confirmar
                     </span>
                </p>`;
            }
            if (array.budget_status_id == 2) {
                tempolateastatus = `
                <p>
                                    <span style="width: 30px;
                                        background: red;
                                        height: 8px;
                                        border-radius: 10px;
                                        margin-right: 0.4rem;
                                        margin-top: 0.2rem;
                                        display: inline-block;">

                                    </span>
                                    <span style="font-size: 12px">Pendiente de aceptar</span>
                                 </p>`;
            }
            if (array.budget_status_id == 3) {
                tempolateastatus = `
                <p>
                                    <span style="width: 30px;
                                        background: green;
                                        height: 8px;
                                        border-radius: 10px;
                                        margin-right: 0.4rem;
                                        margin-top: 0.2rem;
                                        display: inline-block;">

                                    </span>
                                    <span style="font-size: 12px">Aceptado</span>
                                 </p>`;
            }
            if (array.budget_status_id == 6) {
                tempolateastatus = `
                <p>
                                    <span style="width: 30px;
                                        background: blue;
                                        height: 8px;
                                        border-radius: 10px;
                                        margin-right: 0.4rem;
                                        margin-top: 0.2rem;
                                        display: inline-block;">

                                    </span>
                                    <span style="font-size: 12px">Facturado</span>
                                 </p>`;
            }
            if (array.budget_status_id == 7) {
                tempolateastatus = `
                <p>
                                    <span style="width: 30px;
                                        background: black;
                                        height: 8px;
                                        border-radius: 10px;
                                        margin-right: 0.4rem;
                                        margin-top: 0.2rem;
                                        display: inline-block;">

                                    </span>
                                    <span style="font-size: 12px">Facturado parcial</span>
                                 </p>`;
            }

            var tareaImp;
            var descripcionTarea = [];

            if (array.budgetDescripcion.length > 0) {   //si el presupuestoAprobado coindicente actual contiene una ó más descripcionesPresupuesto...
                array.budgetDescripcion.forEach(element => {
                    descripcionTarea.push(element)
                });
            }

            var tareaSingle = [];

            console.log(array.task)
            if(array.task.length > 0) {     //si el presupuestoAprobado coincidente actual contiene una ó más tasks...

                array.task.forEach(tarea => { //por cada una de las tareas...
                    tareaSingle.push(tarea)
                })
            }
            console.log('tarea single:  ',tareaSingle);

            //template= variable del html que voy a imprimir
            var template = `
                <ul id="budgetUl" class="drag-list" draggable="true" data-order="" data-target="${contador}" data-id="${array.id}">
                    <li class="drag-column drag-column-in-progress" data-id="${array.id}" data-target="${array.id}">
                        <span class="drag-column-header">

                            <h2>${array.client.name}</h2>

                            <svg class="drag-header-more" data-target="options1" fill="#FFFFFF" height="24" viewBox="0 0 24 24" width="24" style="fill:black;">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></svg>
                        </span>${tempolateastatus}<ul class="drag-inner-list" id="1" style="padding-left: 0;">

                            <li class="drag-item" data-toggle="modal" data-id="${array.id}" data-target="#${array.id}">
                                <p class="hide">${array.reference}</p>

                                <p>${array.client.name}</p>

                                <p class="hide">${array.project.name}</p>

                            </li>
                            <div class="modal fade ${array.id}" id="${array.id}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content modalContenido">
                                        <div class="header-modal">
                                            <span class="header-title-modal"><i class="fa-solid fa-address-card"></i> <span style="margin-left:0.6rem;">${array.client['name']}</span></span>
                                            <span class="trello-item-subtitulo">en la lista ${statusBudgets}</span>
                                            <p class="header-title-modal" style="font-size:0.85rem !important; margin-left: 34px; margin-top: 10px">Creado el dia: ${array.created_at}</p>
                                            <a href="/admin/budgets/${array.id}/edit" target="_blank" class="nch-button nch-button--primary confirm mod-submit-edit js-save-edit"> Ir al Presupuesto</a>
                                        </div>

                                        <div class="main-modal">
                                            <div class="js-fill-card-detail-desc">
                                                <div>
                                                    <div class="js-react-root">
                                                        <div class="window-module">
                                                            <div class="window-module-title window-module-title-no-divider description-title">
                                                                <span class="icon-description icon-lg window-module-title-icon">
                                                                    <i class="fa-solid fa-align-left"></i>
                                                                </span>
                                                                <h3 class="u-inline-block">Descripción</h3>

                                                            </div>
                                                        </div>
                                                        <!-- aquí iba -->
                                                        <div class="u-gutter">
                                                            <div attr="desc" class="editable">
                                                                <div class="description-content js-desc-content">
                                                                    <div class="description-content-fade-button">
                                                                    </div>
                                                                    <form id="formDes" action="" data-id="${array.id}">
                                                                        <div dir="auto" class="current markeddown hide-on-edit js-desc js-show-with-desc hide"></div>

                                                                        <div id="textAreaDescripcion" class="description-edit edit">

                                                                            <textarea placeholder="Añadir una descripción más detallada..." class="field field-autosave js-description-draft description card-description"></textarea>
                                                                        </div>
                                                                        <div class="edit-controls u-clearfix">
                                                                            <input id="buttonDescripcion" class="nch-button nch-button--primary confirm mod-submit-edit js-save-edit" type="submit" value="Guardar">
                                                                            <a class="icon-lg icon-close dark-hover cancel js-cancel-edit" href="#"></a>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        ${templeNotas(descripcionTarea)}
                                                    </div>
                                                    <div class="activida">
                                                        <div class="window-module">
                                                            <div class="window-module-title window-module-title-no-divider description-title">
                                                                <span class="icon-description icon-lg window-module-title-icon">
                                                                    <i class="fa-solid fa-bars-progress"></i>
                                                                </span>
                                                                <h3 class="u-inline-block">Tareas</h3>
                                                            </div>
                                                            <div class="tareas-trello">
                                                                <ul class="drag-inner-list" id="9">${tempolateastatus2(tareaSingle)}</ul>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </ul>
                    </li>

                </ul>
                </li>
                </ul>`;
                console.log(descripcionTarea);
                divContainer.innerHTML += template;

        })

        descripcionTrollo()


        console.log('¡Nuevo array: ',nuevoArray)
    }

    $(document).ready(function() {
        $("#budgetUl").on("drag", function(e) {
            e.preventDefault();
            console.log('entro')
            $("html, body").scrollLeft(50);
        });







        function handleDragStart(e) {
            this.style.opacity = '0.4';
            dragSrcEl = this;

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        }

        function handleDragEnd(e) {
            this.style.opacity = '1';

            items.forEach(function(item) {
                item.classList.remove('over');
            });
        }

        function handleDrop(e) {
            e.stopPropagation(); // stops the browser from redirecting.

            if (dragSrcEl !== this) {
                dragSrcEl.innerHTML = this.innerHTML;
                this.innerHTML = e.dataTransfer.getData('text/html');
            }


            var data_send = {
                order: []
            };
            var listasColumnas = document.querySelectorAll('#budgetUl');

            listasColumnas.forEach(lista => {
                var nodoLi = lista.querySelector('li');
                var id = nodoLi.getAttribute('data-id');
                var orden = lista.getAttribute('data-target');

                data_send.order.push({
                    id: id,
                    orden: orden
                })

            })

            console.log(data_send)


            $.when(changeStatus(data_send)).then(function(response) {
                console.log(response);
            });

            return false;
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }

            return false;
        }

        function handleDragEnter(e) {
            this.classList.add('over');
        }

        function handleDragLeave(e) {
            this.classList.remove('over');
        }

        let items = document.querySelectorAll('.drag-container .drag-list');
        items.forEach(function(item) {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('drop', handleDrop);
        });
    });


    $(document).ready(function() {

    });

    function setNuevaNota(gestor, tareaId, descripcion, user) {
        return $.ajax({
            type: "POST",
            url: '/admin/budgets/management/setNuevaNota',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                'gestor': gestor,
                'tareaId': tareaId,
                'descripcion': descripcion,
                'user': user
            },
            dataType: "json"
        });
    }

    function changeStatus(data_send) {
        return $.ajax({
            type: "POST",
            url: 'management/changeOrder',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: data_send,
            dataType: "json"
        });
    }

    function addDescripcion(data) {
        return $.ajax({
            type: "POST",
            url: '/admin/budgets/management/addDescripcion',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: data,
            dataType: "json",
        });
    }

    function actualizarDom() {
        return $.ajax({
            type: "POST",
            url: '/admin/budgets/management/actualizarDom',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            dataType: "json",
        });
    }
</script>

@endsection
