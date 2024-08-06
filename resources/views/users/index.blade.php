@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-person"></i> Usuarios</h3>
                    <p class="text-subtitle text-muted">Listado de usuarios - empleados</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                        </ol>
                    </nav>

                </div>
            </div>
            {{-- <div class="row mt-3">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    @if($usuarios->count() > 0)
                        <a href="{{route('users.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus me-2 mx-auto"></i>  Crear Usuario</a>
                    @endif
                </div>
            </div> --}}
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
                    @else
                        {{-- Contenido para dispositivos de escritorio --}}
                        {{-- <livewire:users-table-view> --}}
                        @livewire('users-table')
                    @endif
                </div>
            </div>

        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

    <script>

        // const queries = {
        //     terms: 'admin',
        //     columns: [0]
        // }
        // dataTable.search(queries.terms, [2]);
        function botonAceptar(id){
            $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                if (data.error) {
                    Toast.fire({
                        icon: "error",
                        title: data.mensaje
                    })
                } else {
                    Toast.fire({
                        icon: "success",
                        title: data.mensaje
                    })

                    setTimeout(() => {
                        location.reload()
                    }, 4000);
                }
            });
        }
        function getDelete(id) {
            const url = '{{route("users.delete")}}'
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

