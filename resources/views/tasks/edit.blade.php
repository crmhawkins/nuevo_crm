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

    <!-- Mostrar errores de validación -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                                    <label for="description">Presupuesto:</label>
                                    <p type="text" class="form-control"><a href="{{route('presupuesto.edit', $task->budget_id )}}" target="_blank" rel="noopener noreferrer">{{ $task->presupuesto->reference }}</a></p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description">Gestor:</label>
                                    <input type="text" class="form-control" value="{{ $task->gestor->name }}"readonly>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description">Cliente:</label>
                                    <input type="text" class="form-control" value="{{ $task->presupuesto->cliente->name }}"readonly>
                                </div>

                                @if($task->split_master_task_id == null)
                                <div class="col-12 mb-3">
                                    <label for="extra_employee">Asignar Empleado</label>
                                    <button type="button" id="addExtraEmployee" class="btn btn-info btn-sm ml-2" @if($timeExceeded) disabled @endif><i class="fas fa-plus"></i></button>

                                    @if($timeExceeded)
                                        <div class="alert alert-warning mt-2">
                                            <strong>⚠️ Advertencia:</strong> El tiempo total asignado ({{ $totalAssignedTime ? seconds_to_time($totalAssignedTime) : '00:00:00' }}) ya supera el tiempo del presupuesto ({{ $task->total_time_budget }}). No se pueden crear más tareas secundarias.
                                        </div>
                                    @endif

                                    @if($errors->has('delete_error'))
                                        <div class="alert alert-danger mt-2">
                                            <strong>❌ Error:</strong> {{ $errors->first('delete_error') }}
                                        </div>
                                    @endif

                                    <div class="alert alert-info mt-2">
                                        <strong>Tiempo total del presupuesto:</strong> {{ $task->total_time_budget ?? $task->estimated_time }}
                                        <br>
                                        <strong>Tiempo asignado actual:</strong> <span id="totalAssignedTime">{{ $totalAssignedTime ? seconds_to_time($totalAssignedTime) : '00:00:00' }}</span>
                                        <br>
                                        <strong>Tiempo consumido:</strong> <span id="totalConsumedTime">{{ $totalConsumedTime ? seconds_to_time($totalConsumedTime) : '00:00:00' }}</span>
                                        <br>
                                        <strong>Tiempo restante:</strong> <span id="remainingTime">{{ $totalBudgetTime > $totalAssignedTime ? seconds_to_time($totalBudgetTime - $totalAssignedTime) : '00:00:00' }}</span>
                                    </div>
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
                                                            <option value="{{$empleado->id}}" @if( $item['id'] == $empleado->id ) selected @endif>{{$empleado->name.' '.$empleado->surname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td ><input type="text" class="form-control estimated-time-input" name="estimatedTime{{$item['num']}}" value="{{$item['horas_estimadas']}}"></td>
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
                                                        @php
                                                            $hasRealHours = !empty($item['horas_reales']) && $item['horas_reales'] != '00:00:00' && $item['horas_reales'] != '0:0:0';
                                                            $disabledAttr = $hasRealHours ? 'disabled title="No se puede eliminar una subtarea con horas reales consumidas"' : '';
                                                        @endphp
                                                        <button type="button" name="remove" id="{{$item['num']}}" class="btn btn-danger btn_remove_mail" {{$disabledAttr}}>X</button>
                                                        <input type="hidden" name="taskId{{$item['num']}}" value="{{$item['task_id']}}">
                                                        <input type="hidden" name="hasRealHours{{$item['num']}}" value="{{$hasRealHours ? '1' : '0'}}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @elseif($task->split_master_task_id != null)
                                <div class="col-12 mb-3">
                                    <label>Empleado, tiempos y estado</label>
                                    <div id="dynamic_field_employee" class="mt-3">
                                        <table class="table-employees table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>H.Estimadas</th>
                                                    <th>H.Reales</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="dynamic-added">
                                                    <td style="width: 250px !important">
                                                        <select class="choices form-select" name="employeeId1" class="form-control">
                                                            <option value="">Empleado</option>
                                                            @foreach($employees as $empleado)
                                                            <option value="{{$empleado->id}}" @if( $task->admin_user_id == $empleado->id ) selected @endif>{{$empleado->name.' '.$empleado->surname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td ><input type="text" class="form-control estimated-time-input" name="estimatedTime1" value="{{$task->estimated_time}}"></td>
                                                    <td ><input type="text" class="form-control" name="realTime1" value="{{$task->real_time}}"></td>
                                                    <td style="width: 200px !important">
                                                        <select class="choices form-select" name="status1" class="form-control">
                                                            <option  value="">-- Seleccione --</option>
                                                            @foreach($status as $s)
                                                            <option value="{{$s->id}}" @if($s->id == $task->task_status_id) selected @endif>{{$s->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="numEmployee" id="numEmployee" value="1">
                                    <input type="hidden" name="taskId1" value="{{$task->id}}">
                                </div>
                                @endif
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
        var totalTimeBudgethourformat = '{{ $task->total_time_budget ?? $task->estimated_time }}'; // Tiempo total estimado de la tarea
        if(totalTimeBudgethourformat != null && totalTimeBudgethourformat != '' && totalTimeBudgethourformat != '0'){
            var totalTimeParts = totalTimeBudgethourformat.split(':');
            var totalTimeBudget = parseInt(totalTimeParts[0]) + (parseInt(totalTimeParts[1]) / 60) + (parseInt(totalTimeParts[2]) / 3600);
        }else{
            var totalTimeBudget = 0;
        }
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

        // Función para convertir tiempo en formato HH:MM:SS a segundos
        function timeToSeconds(time) {
            if (!time) return 0;
            var parts = time.split(':');
            if (parts.length >= 3) {
                return (parseInt(parts[0]) * 3600) + (parseInt(parts[1]) * 60) + parseInt(parts[2]);
            }
            return 0;
        }

        // Función para convertir segundos a formato HH:MM:SS
        function secondsToTime(seconds) {
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds % 3600) / 60);
            var secs = seconds % 60;

            return String(hours).padStart(2, '0') + ':' +
                   String(minutes).padStart(2, '0') + ':' +
                   String(secs).padStart(2, '0');
        }

        // Función para calcular el tiempo total asignado (usar el máximo entre estimado y real)
        function calculateTotalAssignedTime() {
            var totalSeconds = 0;
            $('.table-employees tbody tr').each(function() {
                var estimatedTime = $(this).find('input[name^="estimatedTime"]').val();
                var realTime = $(this).find('input[name^="realTime"]').val();
                
                var estimatedSeconds = estimatedTime ? timeToSeconds(estimatedTime) : 0;
                var realSeconds = realTime ? timeToSeconds(realTime) : 0;
                
                // Usar el máximo entre estimado y real para considerar tareas sobrepasadas
                totalSeconds += Math.max(estimatedSeconds, realSeconds);
            });
            return totalSeconds;
        }

        // Función para calcular el tiempo consumido (suma de horas reales)
        function calculateTotalConsumedTime() {
            var totalSeconds = 0;
            $('.table-employees tbody tr').each(function() {
                var realTime = $(this).find('input[name^="realTime"]').val();
                if (realTime) {
                    totalSeconds += timeToSeconds(realTime);
                }
            });
            return totalSeconds;
        }

        // Función para actualizar los indicadores de tiempo
        function updateTimeIndicators() {
            var totalAssignedSeconds = calculateTotalAssignedTime();
            var totalConsumedSeconds = calculateTotalConsumedTime();
            var totalBudgetSeconds = timeToSeconds('{{ $task->total_time_budget ?? $task->estimated_time }}');
            var remainingSeconds = totalBudgetSeconds - totalAssignedSeconds;

            $('#totalAssignedTime').text(secondsToTime(totalAssignedSeconds));
            $('#totalConsumedTime').text(secondsToTime(totalConsumedSeconds));
            $('#remainingTime').text(secondsToTime(remainingSeconds > 0 ? remainingSeconds : 0));

            // Cambiar color del tiempo restante si es negativo
            if (remainingSeconds < 0) {
                $('#remainingTime').css('color', 'red').css('font-weight', 'bold');
            } else {
                $('#remainingTime').css('color', 'inherit').css('font-weight', 'normal');
            }
        }

        // Función para calcular el tiempo restante (considerando horas reales)
        function calculateRemainingTime() {
            var totalAssignedTime = 0;

            // Sumar las horas (usando el máximo entre estimado y real) ya asignadas a otros empleados
            $('.table-employees tbody tr').each(function() {
                var estimatedTime = $(this).find('input[name^="estimatedTime"]').val();
                var realTime = $(this).find('input[name^="realTime"]').val();
                
                var estimatedSeconds = estimatedTime ? timeToSeconds(estimatedTime) : 0;
                var realSeconds = realTime ? timeToSeconds(realTime) : 0;
                var maxSeconds = Math.max(estimatedSeconds, realSeconds);
                
                // Convertir a horas decimales
                var timeInHours = maxSeconds / 3600;
                totalAssignedTime += timeInHours || 0;
            });

            // Restar el tiempo asignado del tiempo total disponible
            var remainingTime = totalTimeBudget - totalAssignedTime;
            return remainingTime > 0 ? remainingTime : 0;
        }

        // Actualizar indicadores de tiempo al cargar la página
        updateTimeIndicators();

        // Función para actualizar el estado del botón de eliminar según las horas reales
        function updateDeleteButtonState($row) {
            var realTimeInput = $row.find('input[name^="realTime"]').val();
            var $deleteButton = $row.find('.btn_remove_mail');
            var $hasRealHoursInput = $row.find('input[name^="hasRealHours"]');
            
            var hasRealHours = realTimeInput && realTimeInput !== '00:00:00' && realTimeInput !== '0:0:0' && realTimeInput.trim() !== '';
            
            if (hasRealHours) {
                $deleteButton.prop('disabled', true);
                $deleteButton.attr('title', 'No se puede eliminar una subtarea con horas reales consumidas');
                $deleteButton.css('opacity', '0.5');
                $hasRealHoursInput.val('1');
            } else {
                $deleteButton.prop('disabled', false);
                $deleteButton.removeAttr('title');
                $deleteButton.css('opacity', '1');
                $hasRealHoursInput.val('0');
            }
        }

        // Actualizar indicadores cuando cambie el tiempo estimado o real
        $(document).on('input', '.estimated-time-input', function() {
            updateTimeIndicators();
        });
        
        // Actualizar indicadores y estado del botón cuando cambie el tiempo real
        $(document).on('input', 'input[name^="realTime"]', function() {
            var $row = $(this).closest('tr');
            updateDeleteButtonState($row);
            updateTimeIndicators();
        });
        
        // Actualizar el estado de los botones al cargar la página
        $(document).ready(function() {
            $('.table-employees tbody tr').each(function() {
                updateDeleteButtonState($(this));
            });
        });

        $('#actualizarTarea').click(function(e) {
            e.preventDefault();

            // Validar que no se supere el tiempo del presupuesto (considerando horas reales)
            var totalAssignedSeconds = calculateTotalAssignedTime();
            var totalBudgetSeconds = timeToSeconds('{{ $task->total_time_budget ?? $task->estimated_time }}');

            if (totalAssignedSeconds > totalBudgetSeconds) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'El tiempo total asignado (considerando horas reales: ' + secondsToTime(totalAssignedSeconds) + ') supera el tiempo del presupuesto (' + '{{ $task->total_time_budget ?? $task->estimated_time }}' + ')',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }

            $('form').submit();
        });

        $('#addExtraEmployee').click(function() {
            var remainingTime = calculateRemainingTime(); // Calcula el tiempo restante en decimal

            // Verificar si hay tiempo disponible
            if (remainingTime <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin tiempo disponible',
                    text: 'No hay tiempo restante disponible para asignar más empleados.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            if (i == 0 || $("#estimatedTime" + i).val() != '') {
                i++;
                var formattedTime = convertToTimeFormat(remainingTime); // Convertir el tiempo restante a formato 00:00:00
                $('.table-employees tbody').append(`
                    <tr id="rowEmployee${i}" class="dynamic-added">
                        <td style="width: 250px !important">
                            <select class="choices form-select" name="employeeId${i}" class="form-control">
                                <option value="">Empleado</option>
                                @foreach($employees as $empleado)
                                    <option value="{{$empleado->id}}">{{$empleado->name." ".$empleado->surname}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td ><input type="text" class="form-control estimated-time-input" name="estimatedTime${i}" value="${formattedTime}" placeholder="Horas estimadas"></td>
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
                            <input type="hidden" name="hasRealHours${i}" value="0">
                        </td>
                    </tr>
                `);
                $('#numEmployee').val(i);
                updateTimeIndicators();
            }
        });

        $(document).on('click', '.btn_remove_mail', function() {
            var button_id = $(this).attr("id");
            var $row = $('#rowEmployee' + button_id);
            var hasRealHours = $row.find('input[name^="hasRealHours"]').val() === '1';
            
            // Si no tiene horas reales, verificar si hay horas reales en el input
            if (!hasRealHours) {
                var realTimeInput = $row.find('input[name^="realTime"]').val();
                if (realTimeInput && realTimeInput !== '00:00:00' && realTimeInput !== '0:0:0') {
                    hasRealHours = true;
                }
            }
            
            if (hasRealHours) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se puede eliminar',
                    text: 'Esta subtarea tiene horas reales consumidas y no se puede eliminar directamente.',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            // Si el botón está deshabilitado, no hacer nada
            if ($(this).prop('disabled')) {
                return false;
            }
            
            $row.remove();
            i--;
            $('#numEmployee').val(i);
            updateTimeIndicators();
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
