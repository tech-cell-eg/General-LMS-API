document.addEventListener('DOMContentLoaded', async function () {
    // DOM elements
    const messageContainer = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const statusElement = document.getElementById('connection-status');
    const errorElement = document.getElementById('error-messages');

    // Get metadata
    const userId = document.querySelector('meta[name="user-id"]').content;
    const token = document.querySelector('meta[name="api-token"]').content;
    const recipientId = document.querySelector('meta[name="recipient-id"]').content;

    // Initial state
    statusElement.textContent = 'Connecting...';
    statusElement.className = 'text-yellow-500';

    // Load messages immediately
    await loadMessages();

    // Initialize Echo if available
    if (window.Echo) {
        try {
            // Connection status handlers
            window.Echo.connector.socket.on('connect', () => {
                statusElement.textContent = 'Connected';
                statusElement.className = 'text-green-500';
                console.log('WebSocket connected');
            });

            window.Echo.connector.socket.on('disconnect', () => {
                statusElement.textContent = 'Disconnected';
                statusElement.className = 'text-red-500';
                console.log('WebSocket disconnected');
            });

            // Subscribe to private channel
            window.Echo.private('user.' + userId)
                .listen('.message.new', (e) => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'mb-2 text-end';
                    messageDiv.innerHTML = `<span class="badge bg-secondary">${e.message.message}</span>`;
                    chatBox.appendChild(messageDiv);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });

        } catch (error) {
            console.error('Echo initialization error:', error);
            statusElement.textContent = 'Real-time disabled';
            statusElement.className = 'text-orange-500';
        }
    } else {
        console.warn('Echo not available');
        statusElement.textContent = 'Real-time disabled';
        statusElement.className = 'text-orange-500';
    }

    // Message sending
    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    async function sendMessage() {
        const message = messageInput.value.trim();
        if (message === '') return;

        try {
            const response = await fetch(`/api/chat/messages/${recipientId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ message })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to send message');
            }

            messageInput.value = '';
        } catch (error) {
            errorElement.textContent = error.message;
            setTimeout(() => errorElement.textContent = '', 5000);
        }
    }

    function addMessageToChat(message) {
        // Check if message already exists
        const existing = document.querySelector(`[data-message-id="${message.id}"]`);
        if (existing) return;

        const messageElement = document.createElement('div');
        messageElement.className = `mb-2 p-2 rounded ${message.sender_id == userId ? 'bg-blue-100 ml-auto' : 'bg-gray-100 mr-auto'}`;
        messageElement.dataset.messageId = message.id;
        messageElement.innerHTML = `
            <div class="font-semibold">${message.sender_id == userId ? 'You' : message.sender.name}</div>
            <div>${message.message}</div>
            <div class="text-xs text-gray-500">${new Date(message.created_at).toLocaleTimeString()}</div>
        `;
        messageContainer.appendChild(messageElement);
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    async function loadMessages() {
        try {
            const response = await fetch(`/api/chat/messages/${recipientId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) throw new Error('Failed to load messages');

            const messages = await response.json();
            messageContainer.innerHTML = ''; // Clear loading message

            if (messages.length === 0) {
                messageContainer.innerHTML = '<div class="text-gray-500 text-center py-4">No messages yet</div>';
            } else {
                messages.forEach(addMessageToChat);
            }
        } catch (error) {
            errorElement.textContent = error.message;
            setTimeout(() => errorElement.textContent = '', 5000);
        }
    }
});
