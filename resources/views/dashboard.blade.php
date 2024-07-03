@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')



@endsection
@section('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function() {
        window.Echo.channel('pagina-recarga')
            .listen('RecargarPagina', (e) => {
                if (e.message == 50) {
                    console.log(e.message);
                    location.reload();
                }
            });
    });
</script>
@endsection
