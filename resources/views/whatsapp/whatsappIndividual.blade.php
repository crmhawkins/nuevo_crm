<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat Whatsapp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/iconfont/material-icons.min.css">

    <style>
      body {
        background-color: #3498db;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
      }

      .container {
        margin: 60px auto;
        background: #fff;
        padding: 0;
        border-radius: 7px;
      }

      .profile-image {
        width: 50px;
        height: 50px;
        border-radius: 40px;
      }

      .settings-tray {
        background: #eee;
        padding: 10px 15px;
        border-radius: 7px;
      }
      .settings-tray .no-gutters {
        padding: 0;
      }
      .settings-tray--right {
        float: right;
      }
      .settings-tray--right i {
        margin-top: 10px;
        font-size: 25px;
        color: grey;
        margin-left: 14px;
        transition: 0.3s;
      }
      .settings-tray--right i:hover {
        color: #74b9ff;
        cursor: pointer;
      }


      input {
        border: none;
        border-radius: 30px;
        width: 80%;
      }
      input::placeholder {
        color: #e3e3e3;
        font-weight: 300;
        margin-left: 20px;
      }
      input:focus {
        outline: none;
      }

      .friend-drawer {
        padding: 10px 15px;
        display: flex;
        vertical-align: baseline;
        background: #fff;
        transition: 0.3s ease;
      }
      .friend-drawer--grey {
        background: #eee;
      }
      .friend-drawer .text {
        margin-left: 12px;
        width: 70%;
      }
      .friend-drawer .text h6 {
        margin-top: 6px;
        margin-bottom: 0;
      }
      .friend-drawer .text p {
        margin: 0;
      }
      .friend-drawer .time {
        color: grey;
      }
      .friend-drawer--onhover:hover {
        background: #74b9ff;
        cursor: pointer;
      }
      .friend-drawer--onhover:hover p,
      .friend-drawer--onhover:hover h6,
      .friend-drawer--onhover:hover .time {
        color: #fff !important;
      }
      p.fecha_mensaje {
          font-size: 0.8rem;
          margin-bottom: 5px;
          margin-top: 10px;
      }

      hr {
        margin: 5px auto;
        width: 60%;
      }

      .chat-bubble {
        padding: 10px 14px !important;
        background: #fff;
        margin: 10px 30px;
        border-radius: 9px;
        position: relative;
        animation: fadeIn 1s ease-in;
      }
      .chat-bubble2 {
        padding: 10px 14px !important;
        background: #d9fdd3;
        margin: 10px 30px;
        border-radius: 9px;
        position: relative;
        animation: fadeIn 1s ease-in;
      }
      .chat-bubble2:after {
        content: "";
        position: absolute;
        top: 50%;
        width: 0;
        height: 0;
        border: 20px solid transparent;
        border-bottom: 0;
        margin-top: -10px;
      }
      .chat-bubble:after {
        content: "";
        position: absolute;
        top: 50%;
        width: 0;
        height: 0;
        border: 20px solid transparent;
        border-bottom: 0;
        margin-top: -10px;
      }
      .chat-bubble--left:after {
        left: 0;
        border-right-color: #fff;
        border-left: 0;
        margin-left: -20px;
      }
      .chat-bubble--right:after {
        right: 0;
        border-left-color: #fff;
        border-right: 0;
        margin-right: -20px;
      }
      .chat-bubble2--right:after {
        right: 0;
        border-left-color: #d9fdd3;
        border-right: 0;
        margin-right: -20px;
      }

      @keyframes fadeIn {
        0% {
          opacity: 0;
        }
        100% {
          opacity: 1;
        }
      }
      .offset-md-9 .chat-bubble {
        background: #74b9ff;
        color: #fff;
      }

      .chat-box-tray {
        background: #eee;
        display: flex;
        align-items: baseline;
        padding: 10px 15px;
        align-items: center;
        margin-top: 19px;
        bottom: 0;
        position: absolute;
          width: 100%;
      }
      .chat-box-tray input {
        margin: 0 10px;
        padding: 6px 2px;
      }
      .chat-box-tray i {
        color: grey;
        font-size: 30px;
        vertical-align: middle;
      }
      .chat-box-tray i:last-of-type {
        margin-left: 25px;
      }
      html, body {
      height: 100%;
      margin: 0;
      }

      body {
      background-color: #3498db;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center
      }

      .container {
      height: 90vh; /* Ajusta el contenedor al 100% de la altura de la ventana */
      max-width: 85vw; /* Asegura que el ancho no se desborde */
      overflow: hidden; /* Evita cualquier desbordamiento del contenedor */
      display: flex; /* Habilita la disposición flexible de los elementos internos */
      flex-direction: column; /* Organiza los elementos internos verticalmente */
      }

      .sidebar {
      overflow-y: scroll; /* Habilita desplazamiento vertical si es necesario */
      height: 82%;
      }

      .col-md-4.border-right {
      height: 100%; /* Asegura que este div use todo el alto disponible */
      }

      .chat-panel {
      flex-grow: 1; /* Permite que este div ocupe el espacio restante */
      overflow-y: scroll;
      height: 91%;
      padding-bottom: 5rem;
      background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
      }

      .no-gutters {
          overflow: hidden;
      }

      @media (max-width: 768px) {
          .col-md-4.border-right, .col-md-8 {
              height: 100vh; /* Altura completa */
              width: 100vw; /* Ancho completo */
              position: fixed; /* Posición fija */
              top: 0;
              transition: transform 0.3s ease-in-out; /* Transición suave */
          }
          .col-md-4.border-right {
              z-index: 2; /* Asegura que el sidebar esté sobre el panel de chat */
          }
          .col-md-8 {
              z-index: 1;
              transform: translateX(100%); /* Oculta el panel de chat fuera de la pantalla */
          }
          /* .back-to-sidebar {
              display: block;
              position: absolute;
              top: 10px;
              left: 10px;
              z-index: 5; Asegura que el botón esté visible sobre todo lo demás
          } */
          .container {
              height: 100vh; /* Ajusta el contenedor al 100% de la altura de la ventana */
              max-width: 100vw; /* Asegura que el ancho no se desborde */
              overflow: hidden; /* Evita cualquier desbordamiento del contenedor */
              display: flex; /* Habilita la disposición flexible de los elementos internos */
              flex-direction: column; /* Organiza los elementos internos verticalmente */
              margin: 0 auto !important;
          }
          .back-to-sidebar {

          }
          .material-icons {
              font-size: 3rem !important;
              color: gray;
              margin-right: 1rem;
          }
          .chat-panel {
              flex-grow: 1;
              overflow-y: scroll;
              height: 100%;
              padding-bottom: 8rem;
              background-image: url(https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png);
          }
      }


  </style>
</head>
<body>
    <div class="container">
        <div class="settings-tray">
            <img class="profile-image" src="https://media.istockphoto.com/id/1337144146/es/vector/vector-de-icono-de-perfil-de-avatar-predeterminado.jpg?s=612x612&w=0&k=20&c=YiNB64vwYQnKqp-bWd5mB_9QARD3tSpIosg-3kuQ_CI=" alt="">
            <div class="text">
                <h6>{{$cliente}}</h6>
            </div>
        </div>
        <div class="chat-panel" id="chat-panel">
            @php
            $messages = $resultado;
            usort($messages, function($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });
            @endphp

            @foreach ($messages as $message)
                @if ($message->mensaje)
                    <div class="row no-gutters">
                        <div class="col-md-6 {{ $message->respuesta ? 'chat-bubble chat-bubble--left' : 'chat-bubble2 chat-bubble2--right' }}">
                            {{ $message->mensaje }}
                            <p class="fecha_mensaje"><small>{{ $message->created_at }}</small></p>
                        </div>
                    </div>
                @endif

                @if ($message->respuesta)
                    <div class="row no-gutters justify-content-end">
                        <div class="col-md-6 chat-bubble2 chat-bubble2--right">
                            {{ $message->respuesta }}
                            <p class="fecha_mensaje"><small>{{ $message->created_at }}</small></p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        {{-- <div class="chat-box-tray">
            <textarea id="message-input" placeholder="Escribe un mensaje aquí..." class="form-control" rows="1"></textarea>
            <button id="send-icon" class="border-0 bg-transparent">
                <i class="material-icons send-icon">send</i>
            </button>
            <input type="file" id="image-upload" style="display: none;" accept="image/*">
            <img id="image-preview" src="#" alt="Vista previa de la imagen" style="display: none;">
        </div> --}}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString();
        }

        $(document).ready(function() {
            $(document).on('click', '#send-icon', function() {
                var messageText = $('#message-input').val();
                var imageFile = $('#image-upload').prop('files')[0];
                if (messageText || imageFile) {
                    console.log("Enviar mensaje o imagen");

                    $('#message-input').val('');
                    $('#image-upload').val('');
                    $('#image-preview').attr('src', '#').hide();
                }
                $('#message-input').css('height', 'auto');
            });

            $(document).on('drop', '.chat-box-tray', function(event) {
                event.preventDefault();
                var files = event.originalEvent.dataTransfer.files;
                if (files.length) {
                    $('#image-upload').prop('files', files);
                    readURL(files[0]);
                }
            });

            function readURL(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }

            function initializeDynamicTextarea() {
                var messageInput = $('#message-input');
                function adjustTextareaHeight() {
                    if (messageInput.length) {
                        messageInput.css('height', 'auto');
                        messageInput.css('height', messageInput.prop('scrollHeight') + 'px');
                    }
                }
                adjustTextareaHeight();
                messageInput.off('input').on('input', function() {
                    adjustTextareaHeight();
                });
            }

            initializeDynamicTextarea();
        });
    </script>
</body>
</html>
