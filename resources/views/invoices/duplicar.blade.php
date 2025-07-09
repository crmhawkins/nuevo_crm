@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Duplicar Facturas y Descargar PDFs</h2>
    <form id="duplicarForm" method="POST" action="{{ route('factura.generateClonedPDFs') }}">
        @csrf
        <div class="form-group">
            <label for="refs">Pega aquí las referencias de las facturas (una por línea):</label>
            <textarea class="form-control" id="refs" name="refs" rows="10" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Descargar ZIP de PDFs</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('duplicarForm').onsubmit = function(e) {
        e.preventDefault();
        let refs = document.getElementById('refs').value.trim().split('\n').map(r => r.trim()).filter(r => r);
        if (refs.length === 0) {
            alert('Debes ingresar al menos una referencia.');
            return;
        }
        let form = this;
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        refs.forEach(r => formData.append('refs[]', r));
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Error al generar el ZIP');
            return response.blob();
        })
        .then(blob => {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'facturas_clonadas.zip';
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(err => alert(err.message));
    };
</script>
@endsection 