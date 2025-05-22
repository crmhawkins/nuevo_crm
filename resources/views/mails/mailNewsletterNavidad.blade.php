
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Los Creativos de Hawkins - FELICITACION DE NAVIDAD</title>
    <style>
        body {
            box-sizing: border-box;
            background: white;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            font-family: 'Times New Roman', Times, serif;
        }

        .fondoPage {
            background: black;
            width: 100%;
            height: 50%;
            display: block;
            position: absolute;
            z-index: 500;
        }

        .page {
            background-color: white;
            display: block;
            width: 100%;

            margin: 0 auto;

        }

        img.logo {
            margin: 0 auto;
            display: block;
            margin-bottom: 2rem;
        }

        .texto {
            margin: 4rem 0 2rem;
            text-align: center;
        }
        p{
            font-size: 1.25rem;
        }
        h3 {
            font-size: 1.8rem;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            color: black;
            transition: all 500ms ease-in-out;
        }
        a:hover {
            text-decoration: underline;
        }
        video{
            cursor: pointer;
            margin: 0 auto;
            display: block;
        }
        .iconsContent {
                display: flex;
                margin: 0 auto;
                text-align: center;
                padding: 0;
                justify-content: space-evenly;
        }
        img.icons {
            display: inline-block;
            padding: 0;
        }
        .flex {
            display: block;
            padding: 4rem;
        }
        @media (max-width: 595px){
            .iconsContent {
                display: flex;
                margin: 0 auto;
                text-align: center;
                padding: 0;
                justify-content: space-between;
            }

            img.icons {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    

    <div class="flex">
    
    <div class="page">
        <img class="logo" src="https://i.ibb.co/k1gq0HG/logo.png" alt="Los Creativos de Hawkins">
        <hr/>
        <div class="texto">
            <p>
                Una agencia de comunicación sin clientes, no es una agencia de comunicación…
            </p>
            <p>
                Es cierto que algunos vienen, otros se van, muchos vuelven, y otros se mantienen a nuestro lado apoyándonos mutuamente y afrontando los buenos y no tan buenos momentos. 
            </p>
            <p>
                A todos; a los de ayer, a los de hoy y a los que vendrán: 
            </p>
            <p>
                <h3>FELIZ NAVIDAD / FELICES FIESTAS</h3>
            </p>
            <p>
                *Luchemos juntos por un mejor 2022, más honesto, con salud sobre todo y buenos proyectos.
            </p>
        </div>

        <video src="https://lchawkins.com/FELICITACIONAVIDAD/felicitacionNavidad.mp4" width="100%" height="auto" controls poster="https://i.ibb.co/Fs9Xqx3/Recurso-2-100.jpg"></video>
        <br>
        <br>
        <br>
        <br>
        <hr>
        <br>
        <br>
        <div class="iconsContent">
            <img class="icons" src="https://i.ibb.co/5khMQQw/iconos1.png" alt="">
            <img class="icons" src="https://i.ibb.co/YWtZHff/iconos2.png" alt="">
            <img class="icons" src="https://i.ibb.co/pbCRx2F/iconos3.png" alt="">
            <img class="icons" src="https://i.ibb.co/nmHDZGS/iconos4.png" alt="">
            <img class="icons" src="https://i.ibb.co/t8TJJyx/iconos5.png" alt="">
        </div>
        
        <br>
        <br>
        <hr>
        <br>
        <br>
        <p style="text-align: center;"><a href="">lchawkins.com</a></p>
        <br>
        <br>
    </div>
    </div>
    <img src='https://crmhawkins.com/checkEmail/{{$newsletter->id_newsletter}}' width='1' height='1' border='0' alt='' hidden>

</body>
</html>