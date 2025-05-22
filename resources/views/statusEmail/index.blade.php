@extends('layouts.app')

@section('titulo', 'Listado de Estados de Email')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-6 order-md-1 order-last">
                <h3 class="display-6">Listado de Estados de Email</h3>
            </div>
            <div class="col-md-6 order-md-2 order-first text-md-end">
                <a href="{{ route('admin.statusMail.create') }}" class="btn bg-color-quinto">Crear Estado de Email</a>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Color</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statuses as $status)
                                    <tr>
                                        <td>{{ $status->name }}</td>
                                        <td><span class="badge bg-{{ $status->color ?? 'secondary' }}">Color</span></td>
                                        <td>
                                            <a href="{{ route('admin.statusMail.edit', $status->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                            <form action="{{ route('admin.statusMail.destroy', $status->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
