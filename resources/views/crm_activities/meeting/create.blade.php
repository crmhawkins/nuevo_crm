@extends('layouts.app')

@section('titulo', 'Crear Acta de Reunion')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

<div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-md-12 col-md-6 order-md-1 order-last">
                <h3>Crear reunion</h3>
                <p class="text-subtitle text-muted">Formulario para registrar una reunion</p>
            </div>

            <div class="col-md-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('reunion.index')}}">Reuniones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Reunion</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section mt-4">
        <div class="row">
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <form id="reunionForm" method="POST" action="{{ route('reunion.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-12 my-2 form-group">
                                    <label class="form-label" for="client_id">Clientes</label>
                                    <select name="client_id" id="client_id" class="form-select choices" >
                                        <option value="">-- Seleccione --</option>
                                        @if($clients)
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name .' '. $client->surname }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Contact Persons -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="contacts" class="form-label">Personas de contacto</label>
                                    <select class="form-select"  id="contacts" name="contacts[]" multiple >
                                        <option value="">Buscar ...</option>
                                    </select>
                                </div>

                                <div class="col-md-12 my-2 form-group">
                                    <label for="contacts_email" class="form-label">Añadir contactos (nombre y correo)</label>

                                    <div id="contactFields">
                                        <!-- Campo de contacto inicial -->
                                        <div class="row mb-2">
                                            <div class="col-md-5">
                                                <input type="text" name="contact_names[]" class="form-control" placeholder="Nombre del contacto">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="email" name="contact_emails[]" class="form-control" placeholder="Correo del contacto">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success add-contact">Añadir</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Picker -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="date" class="form-label">Fecha</label>
                                    <input id="date" type="date" name="date" class="form-control" />
                                </div>

                                <!-- Checkbox for Mark as Done -->
                                <div class="col-md-6 my-2 align-items-center justify-content-center flex-column form-group text-center d-flex">
                                    <label class="form-label" for="done">Marcar como realizada</label>
                                    <input type="checkbox" class="form-check-input" id="done" name="done" value="1" style="width: 30px; height: 30px;">
                                </div>

                                <!-- Time Start Picker -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="time_start" class="form-label">Hora de inicio</label>
                                    <input type="time" id="time_start" class="form-control" name="time_start" >
                                </div>

                                <!-- Time End Picker -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="time_end" class="form-label">Hora de finalización</label>
                                    <input type="time" id="time_end" class="form-control" name="time_end" >
                                </div>

                                <!-- Subject -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="subject" class="form-label">Asunto</label>
                                    <input type="text" class="form-control" id="subject" name="subject" >
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea class="form-control visual-editor" id="description" name="description" rows="5" ></textarea>
                                </div>

                                <!-- Mode of Contact -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="contact_by_id" class="form-label">Modalidad</label>
                                    <select name="contact_by_id" class="form-select" id="contact_by_id" >
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($contactBy as $contactByOption)
                                            <option value="{{ $contactByOption->id }}">{{ $contactByOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hawkins Team in Meeting -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="team" class="form-label">Equipo Hawkins Asistente en la reunión</label>
                                    <select class="form-select" id="team" name="team[]" multiple >
                                        <option value="">Buscar ...</option>
                                        @foreach($usuarios as $user)
                                            <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hawkins Team Linked to the Meeting Minutes -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="teamActa" class="form-label">Equipo Hawkins Vinculado al Acta</label>
                                    <select class="form-select" id="teamActa" name="teamActa[]" multiple >
                                        <option value="">Buscar ...</option>
                                        <option value="1" selected="selected"> Iván Fernández Cardosa </option>
                                        @foreach($usuariosActa as $user)
                                            <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- File Upload -->
                                <div class="col-md-12 my-2 form-group">
                                    <label class="form-label" for="formFileMultiple">Archivos</label>
                                    <input class="form-control" type="file" id="formFileMultiple" name="archivos[]" multiple />
                                </div>
                                <input type="hidden" name="audio_filenames[]" id="audio_filenames" />
                                <div class="col-12 mt-2 row">
                                    <audio id="audioPlayback" controls style="display:none;"></audio>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-3">
                <div class="card-body p-3">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                <div class="card-body">
                    <div class="card-body">
                        <button id="guardar" class="btn btn-success btn-block mb-2">Guardar</button>
                        <button id="startRecording" type="button" class="btn btn-dark btn-block mb-2">Iniciar Grabación</button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection


@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const contactFields = document.getElementById('contactFields');

        document.querySelector('.add-contact').addEventListener('click', function () {
            // Crear un nuevo conjunto de campos de nombre y correo
            const newContactRow = document.createElement('div');
            newContactRow.classList.add('row', 'mb-2');

            newContactRow.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="contact_names[]" class="form-control" placeholder="Nombre del contacto">
                </div>
                <div class="col-md-5">
                    <input type="email" name="contact_emails[]" class="form-control" placeholder="Correo del contacto">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-contact">Eliminar</button>
                </div>
            `;

            contactFields.appendChild(newContactRow);

            // Eliminar contactos
            newContactRow.querySelector('.remove-contact').addEventListener('click', function () {
                newContactRow.remove();
            });
        });

        const contactselect = document.getElementById('contacts');
        const clientSelect = document.getElementById('client_id');

        var choicesContacts  = new Choices(contactselect, {
            removeItemButton: true, // Permite a los usuarios eliminar una selección
            searchEnabled: true,  // Habilita la búsqueda dentro del selector
            paste: false          // Deshabilita la capacidad de pegar texto en el campo
        });
        var choicesTeamActa  = new Choices('#teamActa', {
            removeItemButton: true, // Permite a los usuarios eliminar una selección
            searchEnabled: true,  // Habilita la búsqueda dentro del selector
            paste: false          // Deshabilita la capacidad de pegar texto en el campo
        });

        var choicesTeam  = new Choices('#team', {
            removeItemButton: true, // Permite a los usuarios eliminar una selección
            searchEnabled: true,  // Habilita la búsqueda dentro del selector
            paste: false          // Deshabilita la capacidad de pegar texto en el campo
        });


    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('reunionForm');
        const audioInput = document.getElementById('audioInput');
        let mediaRecorder;
        let audioChunks = [];
        let recordingInterval;

        document.getElementById('startRecording').addEventListener('click', function () {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();

                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
                        const audioFile = new File([audioBlob], 'audio.mp3', { type: 'audio/mp3' });

                        // Crear un objeto FormData y añadir el archivo de audio
                        const formData = new FormData();
                        formData.append('audio', audioFile);

                        // Enviar el archivo de audio al servidor
                        fetch('{{ route('audio.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.audio_filename) {
                                console.log('Archivo subido:', data.audio_filename);

                                // Guardar el nombre del archivo en la sesión automáticamente
                                // Actualizar el campo oculto del formulario si lo necesitas después
                                let audioFilenamesField = document.getElementById('audio_filenames');
                                if (audioFilenamesField) {
                                    if (audioFilenamesField.value) {
                                        audioFilenamesField.value += ',' + data.audio_filename;
                                    } else {
                                        audioFilenamesField.value = data.audio_filename;
                                    }
                                }

                                // Mostrar el audio grabado en el reproductor
                                const audioUrl = data.audio_url;
                                document.getElementById('audioPlayback').src = audioUrl;
                                document.getElementById('audioPlayback').style.display = 'block';
                            }
                        })
                        .catch(error => console.error('Error al subir el archivo:', error));

                        // Limpiar los chunks de audio para la próxima grabación
                        audioChunks = [];
                    };

                    // Grabar y enviar cada 15 minutos (900000 ms)
                    recordingInterval = setInterval(() => {
                        if (mediaRecorder.state !== 'inactive') {
                            mediaRecorder.stop(); // Detiene la grabación actual y la sube
                            mediaRecorder.start(); // Inicia una nueva grabación
                        }
                    }, 900000); // 15 minutos

                    document.getElementById('startRecording').disabled = true;
                })
                .catch(error => {
                    console.error("Error al acceder al micrófono", error);
                });
        });



        document.getElementById('guardar').addEventListener('click', function (e) {
            e.preventDefault();
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
            //esperar 10 segundos para que finalice la grabación
            setTimeout(function () {
                form.submit(); // Enviar el formulario normalmente
            }, 10000);
            //form.submit();
        });

        const contactselect = document.getElementById('contacts');
        const clientSelect = document.getElementById('client_id');

        var choicesContacts = new Choices(contactselect, {
            removeItemButton: true,
            searchEnabled: true,
            paste: false
        });

        clientSelect.addEventListener('change', function () {
            const clientId = this.value;
            if (clientId) {
                getContacts(clientId);
            } else {
                choicesContacts.clearChoices();
                choicesContacts.setChoices([{ value: '', label: 'Seleccione un Contacto' }]);
                contactselect.disabled = true;
            }
        });

        function getContacts(clientId) {
            fetch('/client/get-contacts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ client_id: clientId })
            })
                .then(response => response.json())
                .then(contactos => {
                    choicesContacts.clearChoices();
                    choicesContacts.setChoices(
                        contactos.map(contact => ({
                            value: contact.id,
                            label: `${contact.name}`,
                            selected: false,
                            disabled: false
                        })),
                        'value', 'label', false
                    );
                    contactselect.disabled = false;
                });
        }
    });
</script>
@endsection
