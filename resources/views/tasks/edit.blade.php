@extends('layouts.app')

@section('titulo', 'Editar Tarea')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Editar Tarea</h3>
                <p class="text-subtitle text-muted">Formulario para editar una tarea</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('tareas.index')}}">Tareas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar tarea</li>
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
                        <form action="{{route('tarea.update', $task->id)}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="title">Título:</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" @if($task->split_master_task_id == null) readonly @endif>
                                    @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description">Descripción:</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" @if($task->split_master_task_id == null) readonly @endif>{{ old('description', $task->description) }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="extra_employee">Asignar Empleado</label>
                                    <button type="button" id="addExtraEmployee" class="btn btn-info btn-sm ml-2"><i class="fas fa-plus"></i></button>
                                    <div id="dynamic_field_employee" class="mt-3">
                                        <table class="table-employees table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>H.Estimadas</th>
                                                    <th>H.Reales</th>
                                                    <th>Estado</th>
                                                    <th>Borrar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($data)
                                                @foreach ($data as $item)
                                                <tr id="rowEmployee{{$item['num']}}" class="dynamic-added">
                                                    <td style="width: 250px !important">
                                                        <select class="choices form-select" name="employeeId{{$item['num']}}" class="form-control">
                                                            <option value="">Empleado</option>
                                                            @foreach($employees as $empleado)
                                                            <option value="{{$empleado->id}}" @if( $item['id'] == $empleado->id ) selected @endif>{{$empleado->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td ><input type="text" class="form-control" name="estimatedTime{{$item['num']}}" value="{{$item['horas_estimadas']}}"></td>
                                                    <td ><input type="text" class="form-control" name="realTime{{$item['num']}}" value="{{$item['horas_reales']}}"></td>
                                                    <td  style="width: 200px !important">
                                                        <select class="choices form-select" name="status{{$item['num']}}" class="form-control">
                                                            <option  value="">-- Seleccione --</option>
                                                            @foreach($status as $s)
                                                            <option value="{{$s->id}}" @if($s->id == $item['status']) selected @endif>{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td >
                                                        <button type="button" name="remove" id="{{$item['num']}}" class="btn btn-danger btn_remove_mail">X</button>
                                                        <input type="hidden" name="taskId{{$item['num']}}" value="{{$item['task_id']}}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="priority">Prioridad:</label>
                                    <select name="priority" class="form-control">
                                        @foreach($prioritys as $p)
                                        <option value="{{$p->id}}" @if($p->id == $task->priority_id) selected @endif>{{$p->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="estimatedTime">Tiempo Estimado:</label>
                                    <input type="text" name="estimatedTime" class="form-control" value="{{($task->split_master_task_id == null) ? $task->total_time_budget : $task->estimated_time }}">
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="status">Estado:</label>
                                    <select  name="status" class="form-control">
                                        @foreach($status as $s)
                                        <option value="{{$s->id}}" @if($s->id == $task->task_status_id) selected @endif>{{$s->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="numEmployee" id="numEmployee" value="{{count($data)}}">
                                <input type="hidden" name="budgetId" value="{{$task->budget_id}}">
                                <input type="hidden" name="taskId" value="{{$task->id}}">
                            </div>
                        </form>
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
                        <button id="actualizarTarea" class="btn btn-success btn-block mb-2">Actualizar</button>
                        <button id="deleteTask" data-id="{{$task->id}}" class="btn btn-danger btn-block mb-2">Eliminar</button>
                    </div>
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
    $(document).ready(function() {
        var totalTimeBudget = parseFloat('{{ $task->total_time_budget }}'); // Tiempo total estimado de la tarea
        var i = {{ count($data) }};

        // Función para convertir un número decimal de horas a formato 00:00:00
        function convertToTimeFormat(hours) {
            var totalSeconds = Math.floor(hours * 3600); // Convertir horas a segundos
            var hoursPart = Math.floor(totalSeconds / 3600); // Obtener horas
            var minutesPart = Math.floor((totalSeconds % 3600) / 60); // Obtener minutos
            var secondsPart = totalSeconds % 60; // Obtener segundos

            // Formatear a 2 dígitos
            var formattedTime =
                String(hoursPart).padStart(2, '0') + ':' +
                String(minutesPart).padStart(2, '0') + ':' +
                String(secondsPart).padStart(2, '0');

            return formattedTime;
        }

        // Función para calcular el tiempo restante
        function calculateRemainingTime() {
            var totalAssignedTime = 0;

            // Sumar las horas estimadas ya asignadas a otros empleados
            $('.table-employees tbody tr').each(function() {
                var estimatedTime = $(this).find('input[name^="realTime"]').val();
                if (estimatedTime) {
                    // Convertir el valor de horas en formato 00:00:00 a decimal para la suma
                    var timeParts = estimatedTime.split(':');
                    var timeInHours = parseInt(timeParts[0]) + (parseInt(timeParts[1]) / 60) + (parseInt(timeParts[2]) / 3600);
                    totalAssignedTime += timeInHours || 0;
                }
            });

            // Restar el tiempo asignado del tiempo total disponible
            var remainingTime = totalTimeBudget - totalAssignedTime;
            return remainingTime > 0 ? remainingTime : 0;
        }

        $('#actualizarTarea').click(function(e) {
            e.preventDefault();
            $('form').submit();
        });

        $('#addExtraEmployee').click(function() {
            var remainingTime = calculateRemainingTime(); // Calcula el tiempo restante en decimal

            if (i == 0 || $("#estimatedTime" + i).val() != '') {
                i++;
                var formattedTime = convertToTimeFormat(remainingTime); // Convertir el tiempo restante a formato 00:00:00
                $('.table-employees tbody').append(`
                    <tr id="rowEmployee${i}" class="dynamic-added">
                        <td style="width: 250px !important">
                            <select class="choices form-select" name="employeeId${i}" class="form-control">
                                <option value="">Empleado</option>
                                @foreach($employees as $empleado)
                                    <option value="{{$empleado->id}}">{{$empleado->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td ><input type="text" class="form-control" name="estimatedTime${i}" value="${formattedTime}" placeholder="Horas estimadas"></td>
                        <td ><input type="text" class="form-control" name="realTime${i}" value="00:00:00" placeholder="Horas reales"></td>
                        <td style="width: 200px !important">
                            <select class="choices form-select" name="status${i}" class="form-control">
                                <option value="">-- Seleccione --</option>
                                @foreach($status as $s)
                                <option {{2 == $s->id ? 'selected' : '' }} value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td >
                            <button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove_mail">X</button>
                            <input type="hidden" name="taskId${i}" value="temp">
                        </td>
                    </tr>
                `);
                $('#numEmployee').val(i);
            }
        });

        $(document).on('click', '.btn_remove_mail', function() {
            var button_id = $(this).attr("id");
            $('#rowEmployee' + button_id).remove();
            i--;
            $('#numEmployee').val(i);
        });
    });

    $('#deleteTask').on('click', function(e){
            e.preventDefault();
            let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
            botonAceptar(id);
        })

        function botonAceptar(id){
            // Salta la alerta para confirmar la eliminacion
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar esta tarea?",
                html: "<p>Esta acción es irreversible.</p>", // Corrige aquí
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
                // denyButtonText: `No Borrar`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Llamamos a la funcion para borrar el usuario
                    $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                        console.log(data)
                        if (!data.status) {
                            // Si recibimos algun error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            // Todo a ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            })
                            .then(() => {
                                window.location.href = "{{ route('tareas.index') }}";
                            })
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            // Ruta de la peticion
            const url = '{{route("tarea.delete")}}'
            // Peticion
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                },
                dataType: "json"
            });
        }
</script>
@endsection
