<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

<script>

    // Obtener el valor del token CSRF desde la etiqueta meta
    function getCSRFToken() {
        var metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        return null;
    }
    $.ajax({
        url: "{{route('campania.updateFromWindow')}}", // Reemplaza con la URL a la que deseas enviar la solicitud POST
        type: 'POST',
        data: {
            idCliente: {{$clienteId}}
            // Agrega más pares clave-valor según sea necesario
        },
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(), // Agregar el token CSRF a los encabezados
        },
        success: function(response) {
            // Maneja la respuesta del servidor aquí
            console.log('Respuesta del servidor:', response);
        },
        error: function(error) {
            // Maneja los errores de la solicitud aquí
            console.error('Error en la solicitud:', error);
        }
    });
    // Cerramos la ventana
    // window.close();
</script>
