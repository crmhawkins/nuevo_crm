@extends('layouts.app')

@section('titulo', 'Mis Vacaciones')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-6 order-md-1 order-last row">
                    <div class="col-auto">
                        <h3><i class="fa-solid fa-umbrella-beach"></i>Vacaciones</h3>
                        <p class="text-subtitle text-muted">Vacaciones disponibles </p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Vacaciones</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                      {{-- <livewire:users-table-view> --}}
                    @php
                    use Jenssegers\Agent\Agent;

                    $agent = new Agent();
                @endphp
                @if ($agent->isMobile())
                    {{-- Contenido para dispositivos móviles --}}
                    <div>
                        <span>Es movil</span>
                    </div>
                    @livewire('holidays-table')

                @else
                    {{-- Contenido para dispositivos de escritorio --}}
                    {{-- <livewire:users-table-view> --}}
                    @livewire('holidays-table')
                @endif

                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    @include('partials.toast')
<script>

    $('#denyHolidays').on('click', function(e){
        e.preventDefault();
        let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
        botonAceptar(id);
    })

    function botonAceptar(id){
        // Salta la alerta para confirmar la eliminacion
        Swal.fire({
            title: "¿Va a rechazar ésta petición de vacaciones.?",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Rechazar petición",
            cancelButtonText: "Cancelar",
            // denyButtonText: `No Borrar`
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                // Llamamos a la funcion para borrar el usuario
                $.when( denyHolidays(id) ).then(function( data, textStatus, jqXHR ) {
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
                            window.location.href = "{{ route('holiday.admin.petitions') }}";
                        })
                    }
                });
            }
        });
    }

    function denyHolidays(id) {
        // Ruta de la peticion
        const url = '{{route("holiday.admin.denyHolidays")}}';
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

