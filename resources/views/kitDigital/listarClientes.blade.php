@extends('layouts.app')

@section('titulo', 'Kit Digital - Listar Clientes')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<style>
        /* Estilos específicos para la tabla */
    .table-responsive {
        overflow-x: auto; /* Asegura un desplazamiento suave en pantallas pequeñas */
        overflow-y: hidden; /* Asegura un desplazamiento suave en pantallas pequeñas */
    }

    .header-table th {
        vertical-align: bottom; /* Alinea el texto de los encabezados en la parte inferior */
        white-space: nowrap; /* Evita que los encabezados se rompan en líneas */
        font-size: 0.85rem; /* Ajusta el tamaño del texto para los encabezados */
    }

    .table td, .table th {
        padding: 0.5rem; /* Ajusta el padding para las celdas */
    }

    .long-text {
        max-width: 250px; /* Máximo ancho para el texto largo */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    th {
    white-space: nowrap !important;
    }
    .titulo_filtros {
    white-space: nowrap !important;
    }
    /* Cambia el estilo del select */
    .cliente .choices {
        width: 50px !important;
        margin-bottom: 0 !important;
        font-size: 0.75rem;
        height: fit-content;
    }
    .cliente .choices__inner {
        padding-bottom: 0 !important;
        display: block !important;
        vertical-align: top !important;
        width: 100% !important;
        background-color: transparent !important;
        padding: 0.1rem 0.1rem 0.1rem 0.2rem !important;
        border: 1px solid rgb(175, 175, 175) !important;
        border-radius: 2.5px !important;
        font-size: 0.75rem !important;
        min-height: 0px !important;
        overflow: hidden !important;
        box-shadow: none !important;
    }

    /* Estilo del dropdown */
    .cliente .choices__list {
        width: 200px; /* Cambia el ancho del dropdown */
        max-width: 400px; /* Ajusta el ancho máximo como desees */
    }
    .choices__list.choices__list--single {
        padding: 0.1rem 0.1rem 0.1rem 0.2rem !important;
    }
    .cliente .choices__item.choices__item--choice.choices__item--selectable {
        color: black !important;
        /* Puedes agregar más estilos aquí */
    }
</style>
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    @section('css')
                    <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
                    @endsection

                    <div>
                        <div class="filtros row mb-4">
                            <div class="col-md-12 col-sm-12">
                                <form id="formFiltros" action="{{route('kitDigital.index')}}" method="get">
                                    <div class="flex flex-row justify-center" >
                                        <div class="mb-3 px-2" style="width: 85px">
                                            <label class="titulo_filtros" for="" >Nª</label>
                                            <select name="perPage" class="form-select">
                                                <option {{ $perPage == 10 ? 'selected' : '' }} value="10">10</option>
                                                <option {{ $perPage == 25 ? 'selected' : '' }} value="25">25</option>
                                                <option {{ $perPage == 50 ? 'selected' : '' }} value="50">50</option>
                                                <option {{ $perPage == 'all' ? 'selected' : '' }} value="all">Todo</option>
                                            </select>
                                        </div>
                                        <div class="w-20 mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Buscar</label>
                                            <input name="buscar" type="text" class="form-control w-100" value="{{old('buscar',$buscar)}}" placeholder="Escriba la palabra a buscar...">
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 200px">
                                            <label class="titulo_filtros" for="">Clientes</label>
                                            <select name="selectedCliente" class="form-select choices">
                                                <option value=""> Seleccione un cliente </option>
                                                @foreach ($clientes as $cliente)
                                                    <option {{$selectedCliente == $cliente->id ? 'selected' : ''}} value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Gestor</label>
                                            <select name="selectedGestor" id="selectedGestor" class="form-select">
                                                <option value=""> Gestor </option>
                                                @foreach ($gestores as $gestor)
                                                    <option {{$selectedGestor == $gestor->id ? 'selected' : ''}} value="{{$gestor->id}}">{{$gestor->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros"for="">Comercial</label>
                                            <select  name="selectedComerciales" id="selectedComerciales" class="form-select">
                                                <option value=""> Comercial </option>
                                                @foreach ($comerciales as $comercial)
                                                    <option {{$selectedComerciales == $comercial->id ? 'selected' : ''}} value="{{$comercial->id}}">{{$comercial->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Estados</label>
                                            <select name="selectedEstado" id="selectedEstado" class="form-select">
                                                <option value=""> Estado </option>
                                                @foreach ($estados as $estado)
                                                    <option {{$selectedEstado == $estado->id ? 'selected' : ''}} value="{{$estado->id}}">{{$estado->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Servicios</label>
                                            <select  name="selectedServicio" id="selectedServicio" class="form-select">
                                                <option value=""> Servicio </option>
                                                @foreach ($servicios as $servicio)
                                                    <option {{$selectedServicio == $servicio->id ? 'selected' : ''}} value="{{$servicio->id}}">{{$servicio->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Estado de la Factura</label>
                                            <select  name="selectedEstadoFactura" id="selectedEstadoFactura" class="form-select">
                                                <option value=""> Estado </option>
                                                @foreach ($estados_facturas as $estadofactura)
                                                    <option {{$selectedEstadoFactura == $estadofactura['id'] ? 'selected' : ''}} value="{{$estadofactura['id']}}">{{$estadofactura['nombre']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="">Segmento</label>
                                            <select wire:model="selectedSegmento" name="selectedSegmento" id="selectedSegmento" class="form-select">
                                                <option value=""> Segmento </option>
                                                @foreach ($segmentos as $segmento)
                                                    <option {{$selectedSegmento == $segmento['id'] ? 'selected' : ''}} value="{{$segmento['id']}}">{{$segmento['nombre']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="filterByDate">Filtrar por Fecha</label>
                                            <select name="selectedDateField" id="selectedDateField" class="form-select">
                                                <option value="">Seleccione campo de fecha</option>
                                                <option {{$selectedDateField == 'created_at' ? 'selected' : ''}} value="created_at">Fecha de Creación</option>
                                                <option {{$selectedDateField == 'fecha_actualizacion' ? 'selected' : ''}} value="fecha_actualizacion">Fecha de Actualización</option>
                                                <option {{$selectedDateField == 'fecha_acuerdo' ? 'selected' : ''}} value="fecha_acuerdo">Fecha de Acuerdo</option>
                                                <option {{$selectedDateField == 'plazo_maximo_entrega' ? 'selected' : ''}} value="plazo_maximo_entrega">Plazo Máximo</option>
                                                <option {{$selectedDateField == 'banco' ? 'selected' : ''}} value="banco">En banco</option>
                                                <!-- Puedes agregar más opciones según los campos de fecha disponibles -->
                                            </select>
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="date_from">Desde</label>
                                            <input value="{{old('date_from',$dateFrom)}}"  type="date" name="date_from" id="date_from" class="form-control">
                                        </div>
                                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                            <label class="titulo_filtros" for="date_to">Hasta</label>
                                            <input  value="{{old('dateTo',$dateTo)}}" type="date" name="date_to" id="date_to" class="form-control">
                                        </div>
                                        <input type="hidden" name="sortColumn" id="sortColumn" value="{{old('sortColumn',$sortColumn)}}">
                                        <input type="hidden" name="sortDirection" id="sortDirection" value="{{ old('sortDirection',$sortDirection)}}">
                                    </div>
                                </form>
                                <div class="col-md-12 col-sm-12 text-center">
                                    <span class="fs-3" >Sumatorio: <b>{{ number_format($Sumatorio, 2, ',', '.') .' €'}}</b></span>
                                </div>
                            </div>
                        </div>
                        @if ( $kitDigitals )
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="header-table">
                                    @foreach ([
                                        'empresa' => 'EMP',
                                        'segmento' => 'SEG',
                                        'cliente_id' => 'CLI. A.',
                                        'cliente' => 'CLIENTE',
                                        'mensaje_interpretado' => 'INTERES.',
                                        'mensaje' => 'IA',
                                        'contacto' => 'CONTACTO',
                                        'telefono' => 'TELEFONO',
                                        'expediente' => 'EXPEDIENTE',
                                        'contratos' => 'CONTRATOS',
                                        'servicio_id' => 'SERVICIOS',
                                        'estado' => 'ESTADO',
                                        'created_at' => 'F.CREA.',
                                        'fecha_actualizacion' => 'F.ACT.',
                                        'importe' => 'IMPORTE',
                                        'estado_factura' => 'ESTADO FACTURA',
                                        'banco' => 'EN BANCO',
                                        'fecha_acuerdo' => 'F. ACUERDO',
                                        'plazo_maximo_entrega' => 'PLZ. MAX',
                                        'gestor' => 'GESTOR',
                                        'comercial_id' => 'COMERCIAL',
                                        'comentario' => 'COMENTARIO',
                                        'nuevo_comentario' => 'N. COMENTARIO',
                                        ] as $field => $label)
                                        <th class="px-3">
                                            <a class="sort" data-column="{{$field}}" >
                                                {{ $label }}
                                                @if ($sortColumn == $field)
                                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                                @endif
                                            </a>
                                        </th>
                                    @endforeach
                                </thead>
                                <tbody>
                                    @foreach ($kitDigitals as $item)
                                    <tr wire:key='{{rand()}}' style="--bs-table-bg: {{$item->estados->color}} !important; --bs-table-color: {{$item->estados->text_color}} !important">
                                        <td class="exclude" style="max-width: 50px"> <input data-id="{{$item->id}}" type="text" name="empresa" id="empresa" value="{{ $item->empresa }}" style="max-width: 50px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none; margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                        <td class="exclude" style="max-width: 50px">
                                            <select name="segmento" id="segmento" style="max-width: 50px;padding: 0.1rem 0.1rem 0.1rem 0.2rem; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;" data-id="{{$item->id}}">
                                                <option value="">Seleccione un segmento</option>
                                                @foreach ($segmentos as $segmento)
                                                    <option value="{{$segmento['id']}}" @if($item->segmento == $segmento['id']) selected  @endif>{{$segmento['nombre']}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="width: 50px !important; ">
                                            <div class="d-flex cliente">
                                                <select class="choices" data-id="{{$item->id}}" name="cliente_id" id="cliente_id" style="width: 50px !important; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;">
                                                    <option value="">SC</option>
                                                    @foreach ($clientes as $cliente)
                                                        <option value="{{$cliente->id}}" @if($item->cliente_id == $cliente->id) selected  @endif>{{$cliente->company ?? $cliente->name}}</option>
                                                    @endforeach
                                                </select>
                                                <a href="{{route('clientes.create')}}" target="blank" class="btn btn-sm btn-light ml-1">
                                                    <i class="fa-solid fa-plus"></i>
                                                 </a>
                                            </div>
                                        </td>
                                        <td style="max-width: 70px !important"><input data-id="{{$item->id}}" type="text" name="cliente" id="cliente" value="{{ $item->cliente }}" style="max-width: 70px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem"></td>
                                        <td style="max-width: 50px">
                                            <input disabled data-id="{{$item->id}}" type="text" name="mensaje_interpretado" id="mensaje_interpretado"
                                            value="{{ $item->mensaje_interpretado == 1 ? 'Si' : ($item->mensaje_interpretado == 2 ? 'No se' : ($item->mensaje_interpretado === 0 ? 'No' : ($item->mensaje_interpretado === 3 ? 'Error' : ($item->mensaje_interpretado === 4 ? 'No respondio' : '')))) }}"
                                            style="height: fit-content; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border: none; margin-bottom: 0 !important; font-size: 0.75rem; text-align: center; width: 56px;">
                                        </td>
                                        <td style="max-width: 50px">
                                            {{-- <textarea disabled cols="30" rows="1"  style="margin-bottom: 0; width:100%;">{{ $item->mensaje }}</textarea> --}}
                                            <button type="button" class="btn btn-sm btn-light" onclick="redirectToWhatsapp({{$item->id}})">Ver</button>

                                        </td>
                                        <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="contacto" id="contacto" value="{{ $item->contacto }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                        <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="telefono" id="telefono" value="{{ $item->telefono }}" style="max-width: 50px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                        <td style="max-width: 50px" class="exclude"><input data-id="{{$item->id}}" type="text" name="expediente" id="expediente" value="{{ $item->expediente }}" style="max-width: 50px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                        <td style="max-width: 50px" class="exclude"><input data-id="{{$item->id}}" type="text" name="contratos" id="contratos" value="{{ $item->contratos }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;width: 50px;text-align: center"></td>
                                        <td style="max-width: 50px">
                                            <select name="servicio_id" id="servicio_id" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 60px">
                                                <option value="">Seleccione un servicio</option>
                                                @foreach($servicios as $servicio)
                                                    <option @if($item->servicio_id == $servicio->id) selected  @endif value="{{$servicio->id}}">{{$servicio->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="estado" id="estado" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 60px">
                                                <option value="">Seleccione un estado</option>
                                                @foreach($estados as $estado)
                                                    <option @if($item->estado == $estado->id) selected  @endif value="{{$estado->id}}">{{$estado->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="max-width: 98px"><input data-id="{{$item->id}}" type="date" name="created_at" id="created_at" value="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d')  }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;" disabled></td>
                                        <td style="max-width: 98px"><input data-id="{{$item->id}}" type="date" name="fecha_actualizacion" id="fecha_actualizacion" value="{{ $item->fecha_actualizacion }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                        <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="importe" id="importe" value="{{ $item->importe }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem; text-align: center;width: 50px"></td>
                                        <td style="max-width: 50px" @if($item->estado_factura == 0) style="background-color: #f25757; color: white;" @else style="background-color: #2cbc09; color: white;" @endif >
                                            <select name="estado_factura" id="estado_factura" data-id="{{$item->id}}" style="background-color: {{$item->estado_factura == 1 ? '#2cbc09': '#f25757'}}; color: white;margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem; width: 66px;">
                                                <option value="">Seleccione un estado</option>
                                                    <option @if($item->estado_factura == 1) selected style="height: fit-content;background-color: #2cbc09; color: white;" @endif value="1">Abonada</option>
                                                    <option @if($item->estado_factura == 0) selected style="height: fit-content;background-color: #f25757; color: white;" @endif value="0">No Abonada</option>
                                            </select>
                                        </td>
                                        <td style="max-width: 98px"><input  data-id="{{$item->id}}" type="date" name="banco" id="banco" value="{{ $item->banco }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;max-width: 98px"></td>
                                        <td style="max-width: 98px"><input data-id="{{$item->id}}" type="date" name="fecha_acuerdo" id="fecha_acuerdo" value="{{ $item->fecha_acuerdo }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;max-width: 80px"></td>
                                        <td style="max-width: 80px"><input data-id="{{$item->id}}" type="date" name="plazo_maximo_entrega" id="plazo_maximo_entrega" value="{{ $item->plazo_maximo_entrega }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;max-width: 80px"></td>
                                        <td style="max-width: 80px" class="exclude">
                                            <select name="gestor" id="gestor" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 80px">
                                                <option value="">Seleccione un gestor</option>
                                                @foreach($gestores as $gestor)
                                                    <option @if($item->gestor == $gestor->id) selected  @endif value="{{$gestor->id}}">{{$gestor->name}} {{$gestor->surname}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="max-width: 80px" class="exclude">
                                            <select name="comercial_id" id="comercial_id" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 80px">
                                                <option value="">Seleccione un comercial</option>
                                                @foreach($comerciales as $comercial)
                                                    <option @if($item->comercial_id == $comercial->id) selected  @endif value="{{$comercial->id}}">{{$comercial->name}} {{$comercial->surname}}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                         <td style="min-width: 200px !important"><textarea name="comentario" data-id="{{$item->id}}" cols="30" rows="1" style=" background-color: rgba(255, 255, 255, 0.123) ;margin-bottom: 0; width:100%;">{{ $item->comentario }}</textarea></td>
                                        <td style="min-width: 200px !important"><textarea name="nuevo_comentario" data-id="{{$item->id}}" cols="30" rows="1"  style="background-color: rgba(255, 255, 255, 0.123) ; margin-bottom: 0; width:100%;">{{ $item->nuevo_comentario }}</textarea></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Modal Structure -->
                            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Editar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea id="modal-textarea" class="form-control" rows="4"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            @if($perPage !== 'all')
                            {{ $kitDigitals->appends(request()->except('page'))->links() }}
                            @endif
                            <div class="col-md-12 col-sm-12 text-center" style="margin: 1rem 0">
                                <span class="fs-3" >Sumatorio: <b>{{ number_format($Sumatorio, 2, ',', '.') .' €'}}</b></span>
                            </div>
                        </div>

                        @else
                            <div class="text-center py-4">
                                <h3 class="text-center fs-3">No se encontraron registros de <strong>DOMINIOS</strong></h3>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')

    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
    <script>

        function redirectToWhatsapp(id) {
            window.open(`/kit-digital/whatsapp/${id}`, '_blank');
        }

        $(document).ready(function() {

        $("#sidebar").remove();
        $("#main").css("margin-left", "0px");


        // Función para manejar la actualización de datos
        function handleDataUpdate(id, value, key) {
            $.ajax({
                type: "POST",
                url: "{{ route('kitDigital.updateData') }}", // Asegúrate que esta es la ruta correcta
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    id: id,
                    value: value,
                    key: key
                },
                success: function(data) {
                    if (data.icon === 'success') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });

                        Toast.fire({
                            icon: data.icon, // Corregido: Se agregó una coma al final
                            title: data.mensaje // Corregido: Se agregó una coma al final
                        });
                    }else{
                        Swal.fire({
                            icon: data.icon,
                            title: data.mensaje,
                            confirmButtonText: 'Ok',
                            backdrop: true // Agrega un fondo oscurecido
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'Ha ocurrido un error. Por favor, intenta de nuevo.',
                        confirmButtonText: 'Ok',
                        backdrop: true // Agrega un fondo oscurecido
                    });
                }
            });
        }

        // Detectar cambios en inputs, selects y textareas dentro de la tabla
        $('.table').on('change', 'input, select, textarea', function() {
            var id = $(this).data('id');  // Asegúrate de que cada fila tenga un atributo data-id
            var key = $(this).attr('name');
            var value = $(this).val();
            handleDataUpdate(id, value, key);
        });

        $('#formFiltros').on('change', 'input, select', function(e) {
            e.preventDefault();
            $('#formFiltros').submit(); // Esto envía el formulario.
        });


        $('.sort').on('click', function (e) {
            e.preventDefault();
            // Obtener la columna seleccionada del atributo data-column
            var column = $(this).data('column');
            console.log(column);
            // Obtener el valor actual del formulario
            var currentColumn = $('#sortColumn').val();
            var currentDirection = $('#sortDirection').val();
            // Si la columna seleccionada es la misma, cambiar la dirección
            if (column === currentColumn) {
                var newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                $('#sortDirection').val(newDirection);
            } else {
                // Si es una columna diferente, establecer 'asc' por defecto
                $('#sortDirection').val('desc');
            }

            // Actualizar el valor de la columna seleccionada
            $('#sortColumn').val(column);
            console.log(column);

            // Enviar el formulario
            $('#formFiltros').submit();
        });

    });
    </script>
@endsection
