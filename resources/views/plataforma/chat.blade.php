@extends('layouts.appWhatsapp')

@section('content')
<style>
    .contenedor {
        padding: 0 !important;
        height: calc(100vh - 60px);
        overflow: hidden !important;
    }

    .chat-wrapper {
        display: flex;
        height: 100%;
    }

    .chat-sidebar {
        width: 30%;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-right: 1px solid #ddd;
    }

    .contacts-scroll {
        flex: 1;
        overflow-y: auto;
    }

    .contact-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
    }

    .contact-item:hover {
        background-color: #f8f8f8;
    }

    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        padding: 15px;
        background-color: #fff;
        border-bottom: 1px solid #ddd;
        flex-shrink: 0;
    }

    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #ece5dd;
        display: flex;
        flex-direction: column;
    }

    .chat-input {
        padding: 15px;
        background-color: #fff;
        border-top: 1px solid #ccc;
        flex-shrink: 0;
    }

    .message {
        display: inline-block;
        max-width: 75%;
        margin-bottom: 10px;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.4;
        word-break: break-word;
        white-space: pre-wrap;
        clear: both;
    }

    .message.sent {
        background-color: #dcf8c6;
        margin-left: auto;
        text-align: right;
        border-radius: 7.5px 7.5px 0 7.5px;
    }

    .message.received {
        background-color: #fff;
        margin-right: auto;
        text-align: left;
        border-radius: 7.5px 7.5px 7.5px 0;
    }

    .timestamp {
        display: block;
        font-size: 11px;
        color: #666;
        margin-top: 4px;
    }

    .css-96uzu9 {
        z-index: -1;
    }
</style>

<div class="chat-wrapper">
    <div class="chat-sidebar">
        <div class="contacts-scroll" id="contacts-container">
            <!-- Contactos -->
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <strong id="selected-contact-name">Selecciona un chat</strong>
        </div>

        <div class="chat-messages" id="messages-container">
            <!-- Mensajes -->
        </div>

        <div class="chat-input d-none" id="chat-input">
            <form id="message-form" class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Escribe un mensaje..." id="message-input">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedContactId = null;
let refreshInterval = null;

document.addEventListener('DOMContentLoaded', loadChats);

// Cargar lista de contactos
function loadChats() {
    fetch('/plataforma/get-chats')
        .then(res => res.json())
        .then(chats => {
            const container = document.getElementById('contacts-container');
            container.innerHTML = '';

            chats.forEach(chat => {
                const item = document.createElement('div');
                item.className = 'contact-item';
                item.textContent = chat.name;
                item.onclick = () => selectChat(chat.id, chat.name);
                container.appendChild(item);
            });
        });
}

// Al seleccionar un chat
function selectChat(chatId, chatName) {
    selectedContactId = chatId;
    document.getElementById('chat-input').classList.remove('d-none');
    document.getElementById('selected-contact-name').textContent = chatName;

    if (refreshInterval) {
        clearInterval(refreshInterval);
    }

    fetchAndLoadMessages(chatId);

    refreshInterval = setInterval(() => {
        fetchAndLoadMessages(chatId);
    }, 3000);
}

// Petición y carga de mensajes
function fetchAndLoadMessages(chatId) {
    fetch(`/plataforma/get-chat?chatId=${encodeURIComponent(chatId)}`)
        .then(res => res.json())
        .then(data => {
            loadMessages(data.messages);
        });
}

// Mostrar mensajes
function loadMessages(messages) {
    const container = document.getElementById('messages-container');
    container.innerHTML = '';

    messages.forEach(msg => {
        const div = document.createElement('div');
        const isSent = msg.fromMe === true || msg.from === '34634261382@c.us';

        div.className = `message ${isSent ? 'sent' : 'received'}`;
        div.innerHTML = `${msg.body}<span class="timestamp">${formatTimestamp(msg.timestamp)}</span>`;

        container.appendChild(div);
    });

    scrollToBottom(container);
}

// Enviar mensaje
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const message = input.value.trim();

    if (message && selectedContactId) {
        // Limpiar el input inmediatamente
        input.value = '';

        fetch('/plataforma/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                chatId: selectedContactId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('messages-container');
            const div = document.createElement('div');
            div.className = 'message sent';
            div.innerHTML = `${message}<span class="timestamp">${formatTimestamp(new Date().toISOString())}</span>`;
            container.appendChild(div);
            scrollToBottom(container);
        });
    }
});

// Scroll al final
function scrollToBottom(container) {
    setTimeout(() => {
        container.scrollTop = container.scrollHeight;
    }, 100);
}

// Formato de hora
function formatTimestamp(isoDate) {
    const date = new Date(isoDate);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>
@endsection
