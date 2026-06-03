@extends('layouts.admin')

@section('title', 'Customer Support')
@section('page-title', 'Chat Sessions')

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
    
    .sessions-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .session-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid #e8eee9;
        position: relative;
    }
    
    .session-item:hover {
        background: white;
    }
    
    .session-item.active {
        background: white;
        border-left: 3px solid #2db84f;
    }
    
    .session-avatar {
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
    }
    
    .session-info {
        flex: 1;
        min-width: 0;
    }
    
    .session-user {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 14px;
    }
    
    .session-last {
        font-size: 11px;
        color: #6e7f72;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .unread-badge {
        background: #dc2626;
        color: white;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: bold;
    }
    
    .handled-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        padding: 20px;
        border-bottom: 1px solid #e8eee9;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-header h4 {
        margin: 0;
        color: #1a7a3e;
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
        line-height: 1.45;
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
    
    .message-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 10px;
        margin-top: 8px;
        cursor: pointer;
    }
    
    .message-time {
        font-size: 10px;
        margin-top: 5px;
        opacity: 0.7;
    }
    
    .chat-input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
    }
    
    .chat-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        outline: none;
    }
    
    .file-attach-btn {
        background: #f0faf3;
        border: none;
        width: 42px;
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
    
    .end-session-btn {
        background: #dc2626;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
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
    
    @media (max-width: 768px) {
        .chat-sidebar {
            width: 250px;
        }
        .message {
            max-width: 85%;
        }
    }
</style>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h3>💬 Active Chats</h3>
        </div>
        <div class="sessions-list" id="sessionsList">
            @forelse($sessions as $item)
            <div class="session-item" onclick="selectSession({{ $item['session']->id }}, '{{ $item['user']->name }}')">
                <div class="session-avatar">{{ strtoupper(substr($item['user']->name, 0, 1)) }}</div>
                <div class="session-info">
                    <div class="session-user">{{ $item['user']->name }}</div>
                    <div class="session-last">{{ Str::limit($item['last_message']->message ?? 'No messages', 30) }}</div>
                </div>
                @if($item['unread_count'] > 0)
                    <span class="unread-badge">{{ $item['unread_count'] }}</span>
                @endif
                @if($item['is_handled'])
                    <span class="handled-badge">{{ $item['handled_by'] ? 'You' : 'Taken' }}</span>
                @endif
            </div>
            @empty
            <div class="no-chat-selected" style="padding: 20px; text-align: center;">No active chat sessions</div>
            @endforelse
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <h4 id="selectedUserName">Select a chat session</h4>
            <button class="end-session-btn" id="endSessionBtn" onclick="showEndSessionModal()" style="display: none;">🔒 End Session</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="no-chat-selected">
                Select a chat session from the left to start messaging.
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
<div id="endSessionModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; padding: 25px; width: 420px; max-width: 90%;">
        <h3 style="margin-bottom: 10px; color: #dc2626;">🔒 End Chat Session</h3>
        <p style="color: #555; font-size: 13px; margin-bottom: 18px;">
            Are you sure you want to end the session with <strong id="endSessionUserName"></strong>?<br><br>
            A closing message will be automatically sent to the user and they will no longer be able to reply.
        </p>
        <div style="background: #f7faf8; border: 1px solid #e8eee9; border-radius: 8px; padding: 12px; margin-bottom: 18px; font-size: 12px; color: #6e7f72; font-style: italic;">
            📩 The user will receive:<br><br>
            "Your chat session has been officially closed. If you need further assistance, type <em>talk to admin</em> in the AI Assistant."
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button onclick="endSession()" style="background: #dc2626; color: white; border: none; padding: 9px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">✅ Yes, End Session</button>
            <button onclick="closeModal()" style="background: #e8eee9; color: #3c4a3f; border: none; padding: 9px 20px; border-radius: 8px; cursor: pointer;">Cancel</button>
        </div>
    </div>
</div>

<script>
    let currentSessionId = null;
    let currentUserName = null;
    let lastMessageId = 0;
    let pollingInterval = null;
    let messageIds = new Set();
    
    function selectSession(sessionId, userName) {
        if (currentSessionId === sessionId) return;
        
        currentSessionId = sessionId;
        currentUserName = userName;
        lastMessageId = 0;
        messageIds.clear();
        
        document.getElementById('selectedUserName').innerText = userName;
        document.getElementById('chatInputArea').style.display = 'flex';
        document.getElementById('endSessionBtn').style.display = 'block';
        
        document.getElementById('chatMessages').innerHTML = '';
        
        loadMessages();
        startPolling();
        
        document.querySelectorAll('.session-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
    }
    
    function loadMessages() {
        if (!currentSessionId) return;
        
        fetch(`/admin/chat/messages/${currentSessionId}?last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.messages && data.messages.length > 0) {
                        appendNewMessages(data.messages);
                        if (data.latest_id) {
                            lastMessageId = data.latest_id;
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function appendNewMessages(messages) {
        const container = document.getElementById('chatMessages');
        if (!container) return;
        
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
    
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message || !currentSessionId) return;
        
        addMessageToUI(message, true);
        input.value = '';
        
        const formData = new FormData();
        formData.append('message', message);
        formData.append('session_id', currentSessionId);
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
            }
        })
        .catch(error => console.error('Error:', error));
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
        if (!file || !currentSessionId) return;
        
        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('session_id', currentSessionId);
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
            if (currentSessionId) {
                loadMessages();
            }
        }, 3000);
    }
    
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    }
    
    function scrollToBottom() {
        const container = document.getElementById('chatMessages');
        container.scrollTop = container.scrollHeight;
    }
    
    function showEndSessionModal() {
        document.getElementById('endSessionUserName').innerText = currentUserName;
        document.getElementById('endSessionModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('endSessionModal').style.display = 'none';
    }
    
    function endSession() {
        fetch('/admin/chat/end-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ session_id: currentSessionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                lockSessionUI();
                // Remove session from sidebar
                document.querySelectorAll('.session-item').forEach(item => {
                    if (item.classList.contains('active')) item.remove();
                });
            }
        });
    }

    function lockSessionUI() {
        const chatInput   = document.getElementById('chatInput');
        const sendBtn     = document.querySelector('.chat-send');
        const inputArea   = document.getElementById('chatInputArea');
        const endBtn      = document.getElementById('endSessionBtn');
        if (chatInput)  chatInput.disabled  = true;
        if (sendBtn)    sendBtn.disabled    = true;
        if (endBtn)     endBtn.style.display = 'none';
        if (inputArea) {
            inputArea.style.pointerEvents = 'none';
            inputArea.style.opacity       = '0.5';
        }
        // Add ended banner
        const banner = document.createElement('div');
        banner.style.cssText = 'text-align:center;padding:10px;background:#fef2f2;color:#dc2626;font-size:12px;border-top:1px solid #fecaca;';
        banner.innerHTML = '🔒 Session ended. The user can no longer reply.';
        inputArea.parentNode.insertBefore(banner, inputArea);
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
</script>
@endsection