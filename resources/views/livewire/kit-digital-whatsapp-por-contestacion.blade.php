@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

    <div>
        <div class="accordion" id="accordionExample">
            @php
            $grupos = [
                1 => 'Si',
                2 => 'No',
                3 => 'NO SE',
                null => 'Sin Respuesta'  // Tratar valores nulos por separado si es necesario
            ];
            @endphp

            @foreach ($grupos as $codigo => $texto)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $codigo }}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $codigo }}" aria-expanded="true" aria-controls="collapse{{ $codigo }}">
                            Grupo: {{ $texto }} - Total: {{ $kitDigitals->where('mensaje_interpretado', $codigo)->count() }}
                        </button>
                    </h2>
                    <div id="collapse{{ $codigo }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $codigo }}" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Telefono</th>
                                        <th>Mensaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kitDigitals->where('mensaje_interpretado', $codigo) as $detail)
                                        <tr>
                                            <td style="max-width: 70px !important"><input data-id="{{$detail->id}}" type="text" name="cliente" id="cliente" value="{{ $detail->cliente }}" style="max-width: 70px;height: fit-content;background-color: {{$detail->estados->color}}; color: {{$detail->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem"></td>
                                            <td style="max-width: 50px"><input data-id="{{$detail->id}}" type="text" name="telefono" id="telefono" value="{{ $detail->telefono }}" style="max-width: 50px;height: fit-content;background-color: {{$detail->estados->color}}; color: {{$detail->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                                            <td class="row" style="">
                                                <textarea disabled cols="30" rows="1"  style="margin-bottom: 0; width:80%;">{{ $detail->mensaje }}</textarea>
                                                <button type="button"  style="margin-bottom: 0; margin-left:5px; width:18%;" class="btn btn-sm btn-light" onclick="redirectToWhatsapp({{$detail->id}})">Ver</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <style>
        /* Estilos específicos para la tabla */
    .table-responsive {
        overflow-x: auto; /* Asegura un desplazamiento suave en pantallas pequeñas */
    }

    .header-table th {
        vertical-align: bottom; /* Alinea el texto de los encabezados en la parte inferior */
        white-space: nowrap; /* Evita que los encabezados se rompan en líneas */
        font-size: 0.85rem; /* Ajusta el tamaño del texto para los encabezados */
    }

    .table td, .table th {
        padding: 0.5rem; /* Ajusta el padding para las celdas */
    }

    .long-text {
        max-width: 250px; /* Máximo ancho para el texto largo */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    th {
      white-space: nowrap !important;
    }
    .titulo_filtros {
      white-space: nowrap !important;
    }
    </style>
</div>
@section('scripts')

    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>

        function redirectToWhatsapp(id) {
            window.open(`/kit-digital/whatsapp/${id}`, '_blank');
        }

        $(document).ready(function() {

        $("#sidebar").remove();
        $("#main").css("margin-left", "0px");
        // Función para manejar la actualización de datos
        });
@endsection
