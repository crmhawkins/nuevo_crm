<div style="background-color: #dcdcdb;margin: 0 auto;width: 100%;height: auto;text-align:center;padding-bottom:20px;">
        <h1 style="margin:0;text-align: center;font-size: 32px">Los Creativos de Hawkins</h1>
        <div style="background-color: white;margin-left:15px;margin-right:15px;margin-bottom:15px;text-align: center;font-family: Helvetica;height: auto">
            <h1 style="padding-top: 40px;margin:0;">¡SOLICITUD DE VACACIONES!</h1>
            <h1>¡Hola! </h1>            
            
            @if($estado == 1)
            <h2> {{$empleado->name}} ha realizado una nueva petición de vacaciones.</h2><br>

            @elseif($estado == 2)
            <h2> ¡ {{$empleado->name}}, tu petición ha sido aceptada !.</h2><br>

            @elseif($estado == 3)
            <h2> {{$empleado->name}}, tu petición ha sido rechazada.</h2><br>

            @endif


            <hr style="display: block;margin-top: 0.5em;margin-bottom: 0.5em;margin-left: 40px;margin-right: 40px;border-style: inset;border-width: 1px;border-color: #282828">
        </div>
        <br></br>
    </div>