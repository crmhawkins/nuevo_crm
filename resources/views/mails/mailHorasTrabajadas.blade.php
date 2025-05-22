<div style="background-color: #dcdcdb;margin: 0 auto;width: 100%;height: auto;text-align:center;padding-bottom:20px;">
        <h1 style="margin:0;text-align: center;font-size: 32px">Los Creativos de Hawkins</h1>
        <div style="background-color: white;margin-left:15px;margin-right:15px;margin-bottom:15px;text-align: center;font-family: Helvetica;height: auto">
            <h1 style="padding-top: 40px;margin:0;">¡Hola! </h1>
            <h2>Información de horas de esta semana: </h2><br>
            @foreach ($arrayHorasTotal as $horas)

                        <h4> {{ $horas }} </h4> 

            @endforeach

            <hr style="display: block;margin-top: 0.5em;margin-bottom: 0.5em;margin-left: 40px;margin-right: 40px;border-style: inset;border-width: 1px;border-color: #282828">
        </div>
        <br></br>
    </div>