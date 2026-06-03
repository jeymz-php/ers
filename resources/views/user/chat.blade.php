@extends('layouts.app')

@section('title', 'Customer Support')

@section('content')
<style>
    /* Reset and Container */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .user-container {
        display: flex;
        min-height: 100vh;
    }
    
    .user-main {
        flex: 1;
        margin-left: 280px;
        background: #f0faf3;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }
    
    .content-area {
        padding: 25px 30px;
    }
    
    /* Chat Container */
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    /* Chat Header */
    .chat-header {
        padding: 20px;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        text-align: center;
        flex-shrink: 0;
    }
    
    .chat-header h3 {
        margin: 0;
        font-size: 18px;
    }
    
    .chat-header p {
        margin: 5px 0 0;
        font-size: 12px;
        opacity: 0.9;
    }
    
    /* Messages Area */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #f7faf8;
    }
    
    /* Message Bubbles */
    .message {
        display: flex;
        max-width: 75%;
        animation: fadeIn 0.2s ease;
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
        line-height: 1.45;
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
    
    .message-system {
        align-self: center;
        max-width: 80%;
    }
    
    .message-system .message-bubble {
        background: #e8eee9;
        color: #6e7f72;
        font-size: 11px;
        text-align: center;
        border-radius: 20px;
    }
    
    .message-attachment {
        margin-top: 8px;
    }
    
    .message-attachment a {
        color: inherit;
        text-decoration: none;
    }
    
    .message-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 10px;
        margin-top: 8px;
        cursor: pointer;
    }
    
    .message-time {
        font-size: 9px;
        margin-top: 6px;
        opacity: 0.6;
        text-align: center;
    }
    
    .admin-badge {
        font-size: 10px;
        background: #2db84f;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Session Status */
    .session-status {
        padding: 10px 20px;
        background: #fff3cd;
        text-align: center;
        font-size: 12px;
        color: #856404;
        flex-shrink: 0;
    }
    
    /* Input Area */
    .chat-input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
        flex-shrink: 0;
        align-items: center;
    }
    
    .chat-input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        outline: none;
        font-size: 13px;
    }
    
    .chat-input:focus {
        border-color: #2db84f;
    }
    
    .chat-input:disabled {
        background: #f0faf3;
        cursor: not-allowed;
    }
    
    .file-attach-btn {
        background: #f0faf3;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.2s;
    }
    
    .file-attach-btn:hover {
        background: #2db84f;
        color: white;
    }
    
    .chat-send {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        transition: transform 0.2s;
    }
    
    .chat-send:hover {
        transform: scale(1.05);
    }
    
    .no-messages {
        text-align: center;
        padding: 40px;
        color: #b0bdb3;
    }
    
    /* Responsive Styles */
    @media (max-width: 1024px) {
        .user-main {
            margin-left: 0;
            width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .content-area {
            padding: 15px;
            padding-top: 70px;
        }
        
        .chat-container {
            height: calc(100vh - 140px);
            border-radius: 16px;
        }
        
        .chat-header {
            padding: 15px;
        }
        
        .chat-header h3 {
            font-size: 16px;
        }
        
        .chat-header p {
            font-size: 11px;
        }
        
        .chat-messages {
            padding: 15px;
            gap: 12px;
        }
        
        .message {
            max-width: 85%;
        }
        
        .message-bubble {
            font-size: 12px;
            padding: 8px 12px;
        }
        
        .message-image {
            max-width: 150px;
            max-height: 150px;
        }
        
        .chat-input-area {
            padding: 12px;
        }
        
        .chat-input {
            padding: 10px 14px;
            font-size: 12px;
        }
        
        .file-attach-btn, .chat-send {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }
        
        .session-status {
            padding: 8px 12px;
            font-size: 11px;
        }
    }
    
    @media (max-width: 480px) {
        .content-area {
            padding: 10px;
            padding-top: 60px;
        }
        
        .chat-container {
            height: calc(100vh - 120px);
        }
        
        .message-bubble {
            font-size: 11px;
            padding: 7px 10px;
        }
        
        .message-time {
            font-size: 8px;
        }
        
        .message-image {
            max-width: 120px;
            max-height: 120px;
        }
        
        .chat-input {
            padding: 8px 12px;
            font-size: 11px;
        }
        
        .file-attach-btn, .chat-send {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="chat-container">
                <div class="chat-header">
                    <h3>💬 Customer Support</h3>
                    <p>Our team will respond to your message shortly</p>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <div class="no-messages">
                        💬 No messages yet.<br>
                        Type your message below to start a conversation.
                    </div>
                </div>
                
                @if($activeSession && !$activeSession->is_active)
                <div class="session-status">
                    🔒 This chat session has ended. Type "talk to admin" in the AI Assistant to start a new conversation.
                </div>
                @endif
                
                <div class="chat-input-area">
                    <button class="file-attach-btn" onclick="document.getElementById('fileInput').click()">📎</button>
                    <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf" onchange="uploadFile(this)">
                    <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." onkeypress="handleKeyPress(event)" {{ (!$activeSession || !$activeSession->is_active) ? 'disabled' : '' }}>
                    <button class="chat-send" onclick="sendMessage()" {{ (!$activeSession || !$activeSession->is_active) ? 'disabled' : '' }}>➤</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let lastMessageId = 0;
    let pollingInterval = null;
    let messageIds = new Set();
    let sessionActive = {{ $activeSession && $activeSession->is_active ? 'true' : 'false' }};
    
    function loadMessages() {
        fetch(`/user/chat/messages?last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.messages && data.messages.length > 0) {
                        appendNewMessages(data.messages);
                        if (data.latest_id) {
                            lastMessageId = data.latest_id;
                        }
                    }
                    sessionActive = data.session_active;
                    
                    // Update input state
                    const chatInput = document.getElementById('chatInput');
                    const sendBtn = document.querySelector('.chat-send');
                    if (chatInput && sendBtn) {
                        chatInput.disabled = !sessionActive;
                        sendBtn.disabled = !sessionActive;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function appendNewMessages(messages) {
        const container = document.getElementById('chatMessages');
        if (!container) return;
        
        if (container.querySelector('.no-messages')) {
            container.innerHTML = '';
        }
        
        messages.forEach(msg => {
            if (messageIds.has(msg.id)) return;
            messageIds.add(msg.id);
            
            const isSent = msg.sender_id === {{ Auth::id() }};
            const isSystem = msg.sender_id === null;
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
            
            let messageClass = isSent ? 'sent' : (isSystem ? 'message-system' : 'received');
            let adminBadge = '';
            
            if (!isSent && !isSystem && msg.sender && msg.sender.role !== 'user') {
                adminBadge = `<span class="admin-badge">Admin</span>`;
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${messageClass}`;
            messageDiv.innerHTML = `
                <div class="message-bubble">
                    ${adminBadge}
                    ${escapeHtml(msg.message)}
                    ${attachmentHtml}
                    <div class="message-time">${time}</div>
                </div>
            `;
            container.appendChild(messageDiv);
        });
        
        scrollToBottom();
    }
    
    function sendMessage() {
        if (!sessionActive) {
            alert('This chat session has ended. Please type "talk to admin" in the AI Assistant to start a new conversation.');
            return;
        }
        
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        addMessageToUI(message, true);
        input.value = '';
        
        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        fetch('/user/chat/send', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.last_id) {
                messageIds.add(data.last_id);
                lastMessageId = data.last_id;
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
        
        if (container.querySelector('.no-messages')) {
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
        if (!file) return;
        
        if (!sessionActive) {
            alert('This chat session has ended. Please type "talk to admin" in the AI Assistant to start a new conversation.');
            input.value = '';
            return;
        }
        
        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        addMessageToUI(`📎 Uploading: ${file.name}...`, true);
        
        fetch('/user/chat/send', {
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
        pollingInterval = setInterval(loadMessages, 3000);
    }
    
    function handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    }
    
    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize
    loadMessages();
    startPolling();
    
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) clearInterval(pollingInterval);
    });
</script>
@endsection