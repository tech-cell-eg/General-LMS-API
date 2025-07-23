<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="api-token" content="{{ $token }}">
    <meta name="recipient-id" content="{{ $recipient->id }}">
    <title>Chat with {{ $recipient->first_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <script>
        // Initialize Echo with the token
        window.EchoConfig = {
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'Authorization': 'Bearer {{ $token }}',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        };
    </script>
</head>

<body>
    <div class="container mt-5">
        <h1>Chat with {{ $recipient->first_name }}</h1>
        <div id="chat-box" class="border p-3" style="height: 400px; overflow-y: scroll;">
            <!-- Messages will be loaded here dynamically -->
        </div>
        <div id="typing-indicator" class="mt-2 text-muted" style="display: none;">{{ $recipient->first_name }} is
            typing...</div>
        <form id="message-form" class="mt-3">
            @csrf
            <div class="input-group">
                <input type="text" id="message-input" class="form-control" placeholder="Type a message...">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function (){
            let recipientId = {{ $recipient->id }};
            let senderId = {{ auth()->id() }};
            let chatBox = document.getElementById('chat-box');
            let messageForm = document.getElementById('message-form');
            let messageInput = document.getElementById('message-input');
            let typingIndicator = document.getElementById('typing-indicator');

            // Function to load messages
            function loadMessages() {
                fetch(`/api/chat/messages/${recipientId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token') // Assuming you're using Sanctum for API auth
                    }
                })
                .then(response => response.json())
                .then(messages => {
                    chatBox.innerHTML = ''; // Clear existing messages
                    messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `mb-2 ${message.sender_id == senderId ? 'text-start' : 'text-end'}`;
                        messageDiv.innerHTML = `<span class="badge ${message.sender_id == senderId ? 'bg-primary' : 'bg-secondary'}">${message.message}</span>`;
                        chatBox.appendChild(messageDiv);
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(error => console.error('Error loading messages:', error));
            }

            // Initial load of messages
            loadMessages();

            // Set user online
            fetch('/online',
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    }
                }
            );

            // subscribe to chat channel
            window.Echo.private('user.' + senderId)
                        .listen('MessageSent', (e) => {
                            // show the message
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'mb-2 text-end';
                            messageDiv.innerHTML = `<span class="badge bg-secondary">${e.message.message}</span>`;
                            chatBox.appendChild(messageDiv);
                            chatBox.scrollTop = chatBox.scrollHeight;
                        });

            // subscribe to typing channel
            window.Echo.private('typing.' + recipientId)
                        .listen('UserTyping', (e) => {
                            if(e.typerId === recipientId){
                                typingIndicator.style.display = 'block';
                                setTimeout(() => typingIndicator.style.display = 'none', 3000);
                            }
                        });

          messageForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const message = messageInput.value;
    if (message) {
        console.log('Attempting to send message:', message); // Debug log

        fetch(`/api/chat/messages/${recipientId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('sanctum_token'),
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            console.log('Message sent successfully:', data); // Debug log
            // Add the new message to the chat box
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mb-2 text-start';
            messageDiv.innerHTML = `<span class="badge bg-primary">${message}</span>`;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Clear the input field
            messageInput.value = '';
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message: ' + (error.message || 'Unknown error'));
        });
    }
});
            let typingTimeOut;
            messageInput.addEventListener('input', function () {
                clearTimeout(typingTimeOut);
                fetch(`/chat/typing`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                typingTimeOut = setTimeout(() => {typingIndicator.style.display = 'none'}, 3000);
            });

            // Set user offline on window close
            window.addEventListener('beforeunload', function () {
                fetch('/offline', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
            });
        });


    </script>
</body>

</html>