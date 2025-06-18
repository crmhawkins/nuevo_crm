@extends('layouts.appWhatsapp')

@section('titulo', 'Subir Excel a Plataforma')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">Subir archivo Excel</div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form action="{{ route('plataforma.upload_excel') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Archivo Excel</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file"
                                    accept=".xlsx,.xls" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Subir y Leer</button>
                        </form>
                    </div>
                </div>
                @if (isset($rows) && count($rows) > 0)
                    <div class="card mt-4">
                        <div class="card-header">Contenido del Excel</div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        @foreach ($rows[0] as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($rows, 1) as $row)
                                        <tr>
                                            @foreach ($row as $cell)
                                                <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
