@extends('layouts.admin')

@section('title', 'Messages')
@section('page-title', 'User Messages')

@section('content')
<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 200px);
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .chat-sidebar {
        width: 320px;
        background: #f7faf8;
        border-right: 1px solid #e8eee9;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    
    .chat-sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #e8eee9;
        background: white;
    }
    
    .chat-sidebar-header h3 {
        margin: 0;
        color: #1a7a3e;
        font-size: 16px;
    }
    
    .user-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .user-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid #e8eee9;
        position: relative;
    }
    
    .user-item:hover {
        background: white;
    }
    
    .user-item.active {
        background: white;
        border-left: 3px solid #2db84f;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .user-info {
        flex: 1;
        min-width: 0;
    }
    
    .user-name {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 14px;
    }
    
    .last-message {
        font-size: 11px;
        color: #6e7f72;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 180px;
    }
    
    .unread-badge {
        background: #dc2626;
        color: white;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: bold;
    }
    
    .session-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 8px;
    }
    
    .session-active {
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    .session-ended {
        background: #e8eee9;
        color: #6e7f72;
    }
    
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    
    .chat-header {
        padding: 20px;
        border-bottom: 1px solid #e8eee9;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .chat-header h4 {
        margin: 0;
        color: #1a7a3e;
        font-size: 16px;
    }
    
    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .status-indicator {
        font-size: 12px;
        color: #2db84f;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .refresh-btn {
        background: #f0faf3;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: #1a7a3e;
    }
    
    .refresh-btn:hover {
        background: #2db84f;
        color: white;
    }
    
    .end-session-btn {
        background: #dc2626;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: white;
    }
    
    .end-session-btn:hover {
        background: #b91c1c;
    }
    
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #f7faf8;
    }
    
    .message {
        display: flex;
        max-width: 70%;
    }
    
    .message.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }
    
    .message.received {
        align-self: flex-start;
    }
    
    .message-bubble {
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
    }
    
    .message.sent .message-bubble {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message.received .message-bubble {
        background: white;
        color: #3c4a3f;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .message-attachment {
        margin-top: 8px;
    }
    
    .message-attachment a {
        color: #2db84f;
        text-decoration: none;
    }
    
    .message-time {
        font-size: 10px;
        margin-top: 5px;
        opacity: 0.7;
        text-align: center;
    }
    
    .message-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        margin-top: 8px;
        cursor: pointer;
    }
    
    .chat-input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }
    
    .chat-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        outline: none;
        font-size: 13px;
    }
    
    .chat-input:focus {
        border-color: #2db84f;
    }
    
    .file-attach-btn {
        background: #f0faf3;
        border: none;
        width: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
    }
    
    .chat-send {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 25px;
        cursor: pointer;
    }
    
    .no-chat-selected {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #b0bdb3;
        text-align: center;
        padding: 20px;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 25px;
        width: 400px;
        max-width: 90%;
    }
    
    .modal-content h3 {
        margin-bottom: 15px;
        color: #1a7a3e;
    }
    
    .modal-content textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        margin-bottom: 15px;
        resize: vertical;
    }
    
    .modal-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .modal-buttons button {
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
    }
    
    .modal-buttons button:first-child {
        background: #dc2626;
        color: white;
        border: none;
    }
    
    .modal-buttons button:last-child {
        background: #6e7f72;
        color: white;
        border: none;
    }
    
    @media (max-width: 768px) {
        .chat-sidebar {
            width: 250px;
        }
        .message {
            max-width: 85%;
        }
        .chat-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .header-actions {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>

<div class="chat-container">
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <h3>💬 Conversations</h3>
        </div>
        <div class="user-list" id="userList">
            @forelse($users as $conversation)
            <div class="user-item" data-user-id="{{ $conversation['user']->id }}" onclick="selectUser({{ $conversation['user']->id }}, '{{ addslashes($conversation['user']->name) }}')">
                <div class="user-avatar">{{ strtoupper(substr($conversation['user']->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ $conversation['user']->name }}</div>
                    <div class="last-message">{{ Str::limit($conversation['last_message']->message ?? 'No messages', 30) }}</div>
                </div>
                @if($conversation['unread_count'] > 0)
                    <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
                @endif
                <span class="session-badge {{ $conversation['session_active'] ? 'session-active' : 'session-ended' }}">
                    {{ $conversation['session_active'] ? 'Active' : 'Ended' }}
                </span>
            </div>
            @empty
            <div class="no-chat-selected" style="padding: 20px; text-align: center;">No conversations yet</div>
            @endforelse
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <h4 id="selectedUserName">Select a user to start chatting</h4>
            <div class="header-actions">
                <div class="status-indicator" id="statusIndicator">
                    <span>●</span> <span id="connectionStatus">Live</span>
                </div>
                <button class="refresh-btn" onclick="refreshMessages()">🔄 Refresh</button>
                <button class="end-session-btn" id="endSessionBtn" onclick="showEndSessionModal()" style="display: none;">🔒 End Session</button>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="no-chat-selected">
                Select a user from the left to view conversation.
            </div>
        </div>
        <div class="chat-input-area" id="chatInputArea" style="display: none;">
            <button class="file-attach-btn" onclick="document.getElementById('fileInput').click()">📎</button>
            <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf" onchange="uploadFile(this)">
            <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." onkeypress="handleKeyPress(event)">
            <button class="chat-send" onclick="sendMessage()">Send</button>
        </div>
    </div>
</div>

<!-- End Session Modal -->
<div id="endSessionModal" class="modal">
    <div class="modal-content">
        <h3>🔒 End Chat Session</h3>
        <textarea id="closingMessage" rows="4" placeholder="Enter closing message for the user..."></textarea>
        <div class="modal-buttons">
            <button onclick="endSession()">Confirm End</button>
            <button onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    let currentUserId = null;
    let currentUserName = null;
    let lastMessageId = 0;
    let pollingInterval = null;
    let messageIds = new Set();
    let currentSessionActive = false;
    
    function selectUser(userId, userName) {
        if (currentUserId === userId) return;
        
        currentUserId = userId;
        currentUserName = userName;
        lastMessageId = 0;
        messageIds.clear();
        
        const userNameElement = document.getElementById('selectedUserName');
        if (userNameElement) {
            userNameElement.innerText = userName;
        }
        
        const chatInputArea = document.getElementById('chatInputArea');
        if (chatInputArea) {
            chatInputArea.style.display = 'flex';
        }
        
        // Clear messages container
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.innerHTML = '';
        }
        
        loadMessages();
        startPolling();
        
        // Update active state in sidebar
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('active');
        });
        const selectedItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active');
        }
    }
    
    function loadMessages() {
        if (!currentUserId) return;
        
        fetch(`/admin/chat/messages/${currentUserId}?last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.messages && data.messages.length > 0) {
                        appendNewMessages(data.messages);
                        if (data.latest_id) {
                            lastMessageId = data.latest_id;
                        }
                    }
                    // Update session status
                    currentSessionActive = data.session_active;
                    updateSessionUI();
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function appendNewMessages(messages) {
        const container = document.getElementById('chatMessages');
        if (!container) return;
        
        // Remove no-chat-selected if present
        if (container.querySelector('.no-chat-selected')) {
            container.innerHTML = '';
        }
        
        messages.forEach(msg => {
            if (messageIds.has(msg.id)) return;
            messageIds.add(msg.id);
            
            const isSent = msg.sender_id === {{ Auth::id() }};
            const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            let attachmentHtml = '';
            if (msg.attachment) {
                const fileUrl = `/storage/${msg.attachment}`;
                if (msg.attachment_type && msg.attachment_type.startsWith('image/')) {
                    attachmentHtml = `<div class="message-attachment"><img src="${fileUrl}" class="message-image" onclick="window.open('${fileUrl}')" alt="Image"></div>`;
                } else {
                    attachmentHtml = `<div class="message-attachment"><a href="${fileUrl}" target="_blank">📎 View Attachment</a></div>`;
                }
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            messageDiv.innerHTML = `
                <div class="message-bubble">
                    ${escapeHtml(msg.message)}
                    ${attachmentHtml}
                    <div class="message-time">${time}</div>
                </div>
            `;
            container.appendChild(messageDiv);
        });
        
        scrollToBottom();
    }
    
    function updateSessionUI() {
        const endSessionBtn = document.getElementById('endSessionBtn');
        if (endSessionBtn) {
            endSessionBtn.style.display = currentSessionActive ? 'block' : 'none';
        }
        
        const statusIndicator = document.getElementById('statusIndicator');
        if (statusIndicator) {
            if (currentSessionActive) {
                statusIndicator.style.color = '#2db84f';
                document.getElementById('connectionStatus').innerText = 'Live';
            } else {
                statusIndicator.style.color = '#dc2626';
                document.getElementById('connectionStatus').innerText = 'Session Ended';
            }
        }
    }
    
    function refreshMessages() {
        if (currentUserId) {
            loadMessages();
        }
    }
    
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message && !currentUserId) return;
        
        addMessageToUI(message, true);
        input.value = '';
        
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', currentUserId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('/admin/chat/send', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.last_id) {
                messageIds.add(data.last_id);
                lastMessageId = data.last_id;
            } else if (data.message) {
                addMessageToUI('❌ ' + data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addMessageToUI('❌ Failed to send. Please try again.', false);
        });
    }
    
    function addMessageToUI(message, isSent) {
        const container = document.getElementById('chatMessages');
        if (!container) return;
        
        if (container.querySelector('.no-chat-selected')) {
            container.innerHTML = '';
        }
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        messageDiv.innerHTML = `
            <div class="message-bubble">
                ${escapeHtml(message)}
                <div class="message-time">${time}</div>
            </div>
        `;
        container.appendChild(messageDiv);
        scrollToBottom();
    }
    
    function uploadFile(input) {
        const file = input.files[0];
        if (!file || !currentUserId) return;
        
        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('receiver_id', currentUserId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('message', '');
        
        addMessageToUI(`📎 Uploading: ${file.name}...`, true);
        
        fetch('/admin/chat/send', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages();
            }
        })
        .catch(error => console.error('Error:', error));
        
        input.value = '';
    }
    
    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        
        pollingInterval = setInterval(() => {
            if (currentUserId) {
                loadMessages();
            }
        }, 2000);
    }
    
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    }
    
    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    function showEndSessionModal() {
        if (!currentSessionActive) return;
        document.getElementById('endSessionModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('endSessionModal').classList.remove('active');
        document.getElementById('closingMessage').value = '';
    }
    
    function endSession() {
        const message = document.getElementById('closingMessage').value;
        if (!message) {
            alert('Please enter a closing message');
            return;
        }
        
        fetch('/admin/chat/end-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                user_id: currentUserId,
                closing_message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Chat session ended successfully');
                closeModal();
                loadMessages();
                currentSessionActive = false;
                updateSessionUI();
            }
        });
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) clearInterval(pollingInterval);
    });
    
    // Auto-select first user if any
    document.addEventListener('DOMContentLoaded', function() {
        const firstUser = document.querySelector('.user-item');
        if (firstUser) {
            const userId = firstUser.getAttribute('data-user-id');
            const userName = firstUser.querySelector('.user-name')?.innerText || 'User';
            if (userId) {
                selectUser(parseInt(userId), userName);
            }
        }
    });
</script>
@endsection