@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<section class="section pt-4 d-flex justify-content-center flex-wrap">
    <div class="card col-12">
        <div class="card-body">
            <div>
                <div class="filtros row mb-4">
                    <div class="col-md-6 col-sm-12">
                        <div class="flex flex-row justify-start">
                            <div class="mr-3">
                                <label for="">Nª por paginas</label>
                                <select wire:model="perPage" class="form-select">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="all">Todo</option>
                                </select>
                            </div>
                            <div class="w-75">
                                <label for="">Buscar</label>
                                <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                            </div>
                        </div>
                    </div>
                </div>

                @if ($holidays->count())
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="header-table">
                                <tr>
                                    <th class="px-3" style="font-size:0.75rem">Usuario</th>
                                    <th class="" style="font-size:0.75rem">Dias Disponibles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($holidays as $holiday)
                                    <tr>
                                        <td>{{$holiday->adminUser->name}}</td>
                                        <td>{{ number_format($holiday->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($perPage !== 'all')
                            {{ $holidays->links() }}
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <h3 class="text-center fs-3">No se encontraron registros de <strong>Vacaciones</strong></h3>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card mt-4 col-12" >
        <div class="card-body">
            <div id="calendar" class="p-4" style="margin-top: 0.75rem; margin-bottom: 0.75rem; overflow-y: auto; border-color:black; border-width: thin; border-radius: 20px;" >
                <!-- Aquí se renderizarán las tareas según la vista seleccionada -->
            </div>
        </div>
    </div>
</section>

@section('scripts')
    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('livewire:load', function () {
                initializeCalendar();
            });
        });

        function initializeCalendar() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return; // Asegúrate de que el elemento existe
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                navLinks: true,
                contentHeight: 600,
                nowIndicator: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: @json($holydayEvents),
            });
            calendar.render();
        }
    </script>
@endsection
