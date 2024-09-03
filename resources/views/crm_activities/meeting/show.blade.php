@extends('layouts.app')

@section('titulo', 'Ver Acta de Reunion')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-md-12 col-md-6 order-md-1 order-last">
                <h3>Ver acta de reunion</h3>
                <p class="text-subtitle text-muted">Detalles de la reunión registrada</p>
            </div>

            <div class="col-md-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('reunion.index')}}">Reuniones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Reunion</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section mt-4">
        <div class="row">
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-group my-2">
                                <label  class="form-label" for="date">Creada por</label>
                                <div class='input-group' style="border:none;padding:0px;">
                                    <input class="form-control" style="width: 0px;" value="{{$meeting->adminUser->name}}" readonly />
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 form-group my-2">
                                <label  class="form-label" for="date">Cliente</label>
                                <div class='input-group' style="border:none;padding:0px;">
                                    <input class="form-control" style="width: 0px;" value="{{$meeting->client->name}}" readonly />
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 form-group my-2">
                                    <label  class="form-label" for="date">Fecha</label>
                                    <div class='input-group' style="border:none;padding:0px;">
                                        <input  id='date' type="date" name="date" class="form-control" style="width: 0px;" value="{{$meeting->date}}" readonly />
                                        <span class="input-group-text" data-target="#date" data-toggle="datetimepicker" >
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 form-group my-2">
                                    <label  class="form-label" for="date">Hora de inicio</label>
                                    <div class='input-group' style="border:none;padding:0px;">
                                        <input type="time" id="time_start" class="form-control timepicker" value="{{$meeting->time_start}}" readonly>
                                        <span class="input-group-text" data-toggle="timepicker" >
                                            <span class="fa fa-clock"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 form-group my-2">
                                    <label  class="form-label" for="date">Hora de finalización</label>
                                    <div class='input-group' style="border:none;padding:0px;">
                                        <input type="time" id="time_end" class="form-control" value="{{$meeting->time_end}}" readonly>
                                        <span class="input-group-text" data-toggle="timepicker" >
                                            <span class="fa fa-clock"></span>
                                        </span>
                                    </div>
                                </div>


                            <div class="col-12 form-group my-2">
                                <label  class="form-label" for="subject">Asunto</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="{{ $meeting->subject }}" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group my-2">
                                <label  class="form-label" for="f-content" class="w-100">Descripción</label>
                                <textarea class="form-control visual-editor" data-full-width-page="true" id="f-content" name="description" rows="10" readonly>{{ $meeting->description }}</textarea>
                            </div>
                            <div class="col-12 form-group my-2">
                                <label  class="form-label" for="address">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $meeting->address }}" readonly>
                            </div>
                            <div class="col-12 form-group my-2">Medio</label>
                                <select name="contact_by_id" class="form-control" id="contact_by_id" readonly>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($contactBy as $contactByOption)
                                    <option value="{{ $contactByOption->id }}"
                                        @if ($contactByOption->id == $meeting->contact_by_id) {{ 'selected'}} @endif
                                        >{{ $contactByOption->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($meeting->files)
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group my-2">
                                <label  class="form-label" for="f-content" class="w-100">Archivos:</label>
                                <ul>
                                @foreach($meeting->files as $file)
                                    <li style="list-style: circle;">
                                        <a href="{{ asset('archivos/'.$file) }}" download>{{$file}}</a>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                            @endif
                            @if(!$comments->isEmpty())
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group my-2">
                                <label><strong>Comentarios del acta:</strong></label>
                                <ul>
                                @foreach($comments as $comment)
                                    <li style="list-style: initial;">
                                        <label class="w-100">Comentario de {{$comment->adminUser->name}}</label>
                                        <textarea class="form-control visual-editor" data-full-width-page="true" id="f-content" name="description" rows="2" readonly>{{$comment->description}}</textarea>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card-body p-3">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-block btn-comment" >
                            Comentar
                        </button>
                        <button type="submit" class="btn btn-info btn-block btn-read" >
                            Ya lo he visto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id ="alreadyRead" method="POST"  action="{{ route('reunion.alreadyRead', $meeting->id) }}" >
    @csrf
</form>

@endsection


@section('scripts')
<!-- JAVASCRIPT -->
<script type="text/javascript">

$(document).ready(function() {

    $('.btn-read').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('#alreadyRead').submit();
    });

    $('.btn-comment').click(function() {
        Swal.fire({
            title: 'Comentar este acta',
            icon: 'question',
            html: `
                <textarea  id="description" style="width: 100%;" class="swal2-textarea m-0 mt-2" rows="4" placeholder="Escribe tu comentario aquí..." required></textarea>
                <div class="swal2-validation-message" id="swal2-validation-message" style="display: none;">El campo está vacío</div>
            `,
            allowEscapeKey: false,
            allowEnterKey: false,
            allowOutsideClick: false,
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: 'Comentar',
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    var text = $("#description").val().trim();
                    console.log(text);
                    if (text) {
                        responseMeeting(text).done(function (data, textStatus, jqXHR) {
                            if (jqXHR.status !== 503) {
                                Swal.fire('Éxito', 'Comentario enviado.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Error al crear el comentario.', 'error');
                            }
                        }).fail(function () {
                            Swal.fire('Error', 'Error al enviar el comentario.', 'error');
                        });
                    } else {
                        Swal.enableButtons();
                        Swal.showValidationMessage('El campo está vacío');
                    }
                });
            }
        });
    });

    function responseMeeting(text) {
        return $.ajax({
            type: "POST",
            url: '{{ route('reunion.addComments', $meeting->id) }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                'texto': text
            },
            dataType: "json"
        });
    }
});

</script>
@endsection
