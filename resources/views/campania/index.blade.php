@extends('layouts.app')

@section('titulo', 'Campañas')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading">

        {{-- Titulos --}}
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Campañas</h3>
                    <p class="text-subtitle text-muted">Listado de campañas</p>
                    {{$campanias->count()}}
                </div>

                <div class="col-12 col-md-4 order-md-1 order-last">

                    @if($campanias->count() >= 0)
                        <a href="{{route('campania.create')}}" class="btn btn-primary"><i class="fa-solid fa-diagram-project me-2 mx-auto"></i>  Crear campaña</a>
                    @endif
                </div>

                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Campañas</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">

                <div class="card-body">
                    <livewire:projects-table-view>
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
            const url = '{{route("cliente.delete")}}'
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

