@extends('layouts.app')

@section('titulo', 'Crear Acta de Reunión')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
@endsection

@section('content')

<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-md-12 col-md-6 order-md-1 order-last">
                <h3>Crear Reunión</h3>
                <p class="text-subtitle text-muted">Formulario para registrar una reunión</p>
            </div>

            <div class="col-md-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reunion.index') }}">Reuniones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Reunión</li>
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
                        <form id="reunionForm" method="POST" action="{{ route('reunion.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- Selección del Cliente -->
                                <div class="col-md-12 my-2 form-group">
                                    <label class="form-label" for="client_id">Clientes</label>
                                    <select name="client_id" id="client_id" class="form-select choices">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name .' '. $client->surname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Personas de contacto -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="contacts" class="form-label">Personas de contacto</label>
                                    <select class="form-select" id="contacts" name="contacts[]" multiple>
                                        <option value="">Buscar ...</option>
                                    </select>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="date" class="form-label">Fecha</label>
                                    <input id="date" type="date" name="date" class="form-control" />
                                </div>

                                <!-- Checkbox de realizada -->
                                <div class="col-md-6 my-2 form-group text-center d-flex flex-column">
                                    <label class="form-label" for="done">Marcar como realizada</label>
                                    <input type="checkbox" class="form-check-input" id="done" name="done" value="1" style="width: 30px; height: 30px;">
                                </div>

                                <!-- Hora de inicio -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="time_start" class="form-label">Hora de inicio</label>
                                    <input type="time" id="time_start" class="form-control" name="time_start">
                                </div>

                                <!-- Hora de finalización -->
                                <div class="col-md-6 my-2 form-group">
                                    <label for="time_end" class="form-label">Hora de finalización</label>
                                    <input type="time" id="time_end" class="form-control" name="time_end">
                                </div>

                                <!-- Asunto -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="subject" class="form-label">Asunto</label>
                                    <input type="text" class="form-control" id="subject" name="subject">
                                </div>

                                <!-- Descripción -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                </div>

                                <!-- Modalidad -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="contact_by_id" class="form-label">Modalidad</label>
                                    <select name="contact_by_id" class="form-select" id="contact_by_id">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($contactBy as $contactByOption)
                                            <option value="{{ $contactByOption->id }}">{{ $contactByOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Equipo Hawkins en la reunión -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="team" class="form-label">Equipo Hawkins Asistente en la reunión</label>
                                    <select class="form-select" id="team" name="team[]" multiple>
                                        <option value="">Buscar ...</option>
                                        @foreach($usuarios as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Equipo Hawkins vinculado al acta -->
                                <div class="col-md-12 my-2 form-group">
                                    <label for="teamActa" class="form-label">Equipo Hawkins Vinculado al Acta</label>
                                    <select class="form-select" id="teamActa" name="teamActa[]" multiple>
                                        <option value="">Buscar ...</option>
                                        @foreach($usuariosActa as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} {{ $user->surname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subir archivos -->
                                <div class="col-md-12 my-2 form-group">
                                    <label class="form-label" for="formFileMultiple">Archivos</label>
                                    <input class="form-control" type="file" id="formFileMultiple" name="archivos[]" multiple />
                                </div>

                                <!-- Audio -->
                                <input type="hidden" id="audioInput" name="audio">
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
                    <button id="guardar" class="btn btn-success btn-block mb-2">Guardar</button>
                    <button id="startRecording" type="button" class="btn btn-dark btn-block mb-2">Iniciar Grabación</button>
                    <button id="stopRecording" type="button" class="btn btn-danger btn-block mb-2" disabled>Detener Grabación</button>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/choices.js/choices.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const contactselect = document.getElementById('contacts');
        const clientSelect = document.getElementById('client_id');
        const audioInput = document.getElementById('audioInput');
        const form = document.getElementById('reunionForm');
        let mediaRecorder;
        let audioChunks = [];

        // Configuración de Choices.js
        const choicesContacts = new Choices(contactselect, { removeItemButton: true, searchEnabled: true });
        const choicesTeamActa = new Choices('#teamActa', { removeItemButton: true, searchEnabled: true });
        const choicesTeam = new Choices('#team', { removeItemButton: true, searchEnabled: true });

        // Grabación de audio
        document.getElementById('startRecording').addEventListener('click', function () {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();

                    mediaRecorder.ondataavailable = event => audioChunks.push(event.data);

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
                        const audioFile = new File([audioBlob], 'audio.mp3');
                        const audioUrl = URL.createObjectURL(audioBlob);
                        document.getElementById('audioPlayback').src = audioUrl;
                        document.getElementById('audioPlayback').style.display = 'block';

                        // Asigna el archivo de audio al input hidden
                        audioInput.files = new DataTransfer().items.add(audioFile);
                    };

                    document.getElementById('startRecording').disabled = true;
                    document.getElementById('stopRecording').disabled = false;
                })
                .catch(error => console.error("Error al acceder al micrófono", error));
        });

        document.getElementById('stopRecording').addEventListener('click', function () {
            mediaRecorder.stop();
            document.getElementById('startRecording').disabled = false;
            document.getElementById('stopRecording').disabled = true;
        });

        document.getElementById('guardar').addEventListener('click', function (e) {
            e.preventDefault();
            form.submit();
        });

        // Manejo del cambio de cliente para cargar contactos
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
                        label: contact.name
                    })),
                    'value', 'label', false
                );
                contactselect.disabled = false;
            });
        }
    });
</script>
@endsection
