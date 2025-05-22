
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500;700&family=IBM+Plex+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <title>Los Creativos de Hawkins - HORARIO DE VERANO</title>
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
        <img class="logo" src="https://i.ibb.co/ydzjGTK/Recurso-1.png" alt="Los Creativos de Hawkins">
        <hr/>
        <div class="texto">
            <h2 style="color: #EA592C">
               Horario de Verano
            </h2>
            <p>Estimados amigos, clientes y futuros clientes,</p>
            <p>Estrenamos horario de verano, así que solo nos encontrarás en la oficina de no, así que solo nos 08:00 h a 15:00 h. Por la tarde estaremos en... <img src="https://i.ibb.co/vwFQg7L/Recurso-14.png" alt=""></p>
            <p>Ahora bien, si necesitas contactar con nosotros con/por urgencia, llámanos a:</p>
            <p></p>
            <p>+34<span class="color: #EA592C">630 625 624</span></p>
            <p>+34<span class="color: #EA592C">605 621 704</span></p>
            <p>+34<span class="color: #EA592C">659 594 307</span></p>
            <p></p>

            <p>
                <img src="https://i.ibb.co/L0kBRLw/Recurso-2.png" alt="">
            </p>
            
        </div>
        <br>
        <br>
        <hr>
        <br>
        <br>
        <p style="text-align: center;"><a href="">lchawkins.com</a></p>
        <br>
        <br>
        <img src="https://i.ibb.co/RBcTHbd/Recurso-3.png" alt="">
    </div>
    </div>
    <img src='https://crmhawkins.com/checkEmail/{{$newsletter->id_newsletter}}' width='1' height='1' border='0' alt='' hidden>

</body>
</html>