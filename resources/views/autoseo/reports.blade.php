@extends('layouts.appWhatsapp')

@section('titulo', 'Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card bg-white shadow position-relative mt-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        Ingresar PIN
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="password" class="form-control" id="pin" name="pin" required autofocus>
                        </div>
                        <button type="button" id="checkPinBtn" class="btn btn-primary">Comprobar</button>
                        <div id="pinResult" class="mt-3"></div>
                        <div id="reportButtons" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function checkPin() {
        console.log('Checkpin')
        const pin = document.getElementById('pin').value;
        const resultDiv = document.getElementById('pinResult');
        const reportDiv = document.getElementById('reportButtons');

        resultDiv.innerHTML = '';
        reportDiv.innerHTML = '';

        fetch('{{ route('autoseo.reports.login') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<div class="alert alert-success">PIN correcto. Reportes encontrados.</div>';

                if (data.reports.length > 0) {
                    const list = document.createElement('div');
                    list.className = "d-grid gap-2";

                    data.reports.forEach(report => {
                        const btn = document.createElement('a');
                        const date = new Date(report.created_at);
                        btn.href = `/autoseo/reports/${report.autoseo_id}/${report.id}`;
                        btn.className = 'btn btn-outline-primary';
                        btn.textContent = `Ver reporte del ${date.toLocaleDateString()} a las ${date.toLocaleTimeString()}`;
                        list.appendChild(btn);
                    });

                    reportDiv.appendChild(list);
                } else {
                    reportDiv.innerHTML = '<div class="alert alert-warning">No hay reportes disponibles.</div>';
                }

            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'PIN incorrecto') + '</div>';
            }
        })
        .catch(error => {
            console.error(error);
            resultDiv.innerHTML = '<div class="alert alert-danger">Error al enviar el PIN</div>';
        });
    }

    // Agrega el listener cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('checkPinBtn').addEventListener('click', checkPin);
    });
</script>
@endsection
