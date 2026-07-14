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

    /* New Chat button + modal */
    .chat-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .new-chat-btn {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .new-chat-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 61, 31, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 4000;
        padding: 20px;
    }

    .new-chat-modal-overlay.active {
        display: flex;
    }

    .new-chat-modal {
        background: white;
        border-radius: 18px;
        width: 100%;
        max-width: 440px;
        max-height: 85vh;
        overflow-y: auto;
        padding: 22px;
    }

    .new-chat-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .new-chat-modal-header h3 {
        color: #1a7a3e;
        font-size: 16px;
    }

    .new-chat-modal-close {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #6e7f72;
    }

    .user-select-dropdown {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 15px;
        background: white;
        color: #3c4a3f;
        cursor: pointer;
    }

    .user-select-dropdown:focus {
        border-color: #2db84f;
        outline: none;
    }

    .new-chat-textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        font-size: 13px;
        resize: vertical;
        min-height: 70px;
        margin-bottom: 15px;
    }

    .start-chat-btn {
        width: 100%;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 11px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
    }

    .start-chat-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .started-by-admin-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4338ca;
    }

    .back-to-list-btn {
        display: none;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #1a7a3e;
        margin-right: 6px;
    }
    
    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 160px);
            border-radius: 14px;
            position: relative;
        }
        .chat-sidebar {
            width: 100%;
        }
        .chat-sidebar,
        .chat-main {
            display: none;
        }
        .chat-container.show-sidebar .chat-sidebar {
            display: flex;
        }
        .chat-container:not(.show-sidebar) .chat-main {
            display: flex;
        }
        .chat-container.show-sidebar .chat-main {
            display: none;
        }
        .back-to-list-btn {
            display: inline-block;
        }
        .message {
            max-width: 85%;
        }
        .chat-header, .chat-input-area {
            padding: 12px 15px;
        }
        .new-chat-btn {
            padding: 6px 10px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .session-avatar {
            width: 38px;
            height: 38px;
            font-size: 15px;
        }
        .message-bubble {
            font-size: 12px;
            padding: 8px 12px;
        }
        .chat-input {
            font-size: 12px;
        }
    }
</style>

<div class="chat-container show-sidebar" id="chatContainer">
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h3>💬 Active Chats</h3>
            <button type="button" class="new-chat-btn" onclick="openNewChatModal()">+ New Chat</button>
        </div>
        <div class="sessions-list" id="sessionsList">
            @forelse($sessions as $item)
            <div class="session-item" id="session-item-{{ $item['session']->id }}" onclick="selectSession({{ $item['session']->id }}, '{{ $item['user']->name }}', this)">
                <div class="session-avatar">{{ strtoupper(substr($item['user']->name, 0, 1)) }}</div>
                <div class="session-info">
                    <div class="session-user">{{ $item['user']->name }}</div>
                    <div class="session-last">{{ Str::limit($item['last_message']->message ?? 'No messages', 30) }}</div>
                </div>
                @if($item['unread_count'] > 0)
                    <span class="unread-badge">{{ $item['unread_count'] }}</span>
                @endif
                @if($item['started_by_admin'])
                    <span class="started-by-admin-badge">You started</span>
                @elseif($item['is_handled'])
                    <span class="handled-badge">{{ $item['handled_by'] ? 'You' : 'Taken' }}</span>
                @endif
            </div>
            @empty
            <div class="no-chat-selected" style="padding: 20px; text-align: center;" id="noSessionsMsg">No active chat sessions</div>
            @endforelse
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div style="display:flex; align-items:center;">
                <button type="button" class="back-to-list-btn" onclick="showSidebarOnMobile()">←</button>
                <h4 id="selectedUserName">Select a chat session</h4>
            </div>
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

<!-- New Chat Modal -->
<div id="newChatModalOverlay" class="new-chat-modal-overlay">
    <div class="new-chat-modal">
        <div class="new-chat-modal-header">
            <h3>💬 Start a New Chat</h3>
            <button type="button" class="new-chat-modal-close" onclick="closeNewChatModal()">&times;</button>
        </div>

        <div id="newChatAlert"></div>

        <label style="display:block; font-size:11px; font-weight:700; color:#1a7a3e; margin-bottom:6px;">Select ERS User</label>
        <select class="user-select-dropdown" id="newChatUserSelect" onchange="onUserDropdownChange(this)">
            <option value="">Loading users...</option>
        </select>

        <textarea class="new-chat-textarea" id="newChatMessage" placeholder="Type your first message..."></textarea>

        <button type="button" class="start-chat-btn" id="startChatBtn" onclick="startNewChat()" disabled>🚀 Start Chat</button>
    </div>
</div>

<script>
    let currentSessionId = null;
    let currentUserName = null;
    let lastMessageId = 0;
    let pollingInterval = null;
    let sessionsPollingInterval = null;
    let messageIds = new Set();
    let selectedNewChatUserId = null;

    function selectSession(sessionId, userName, el) {
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
        if (el) {
            el.classList.add('active');
        } else {
            const item = document.getElementById(`session-item-${sessionId}`);
            if (item) item.classList.add('active');
        }

        // Mobile: switch to the conversation pane
        document.getElementById('chatContainer').classList.remove('show-sidebar');
    }

    function showSidebarOnMobile() {
        document.getElementById('chatContainer').classList.add('show-sidebar');
    }
    
    // ===== New Chat Modal =====
    let usersDropdownLoaded = false;

    function openNewChatModal() {
        document.getElementById('newChatModalOverlay').classList.add('active');
        document.getElementById('newChatMessage').value = '';
        document.getElementById('startChatBtn').disabled = true;
        selectedNewChatUserId = null;
        loadUsersForDropdown();
    }

    function closeNewChatModal() {
        document.getElementById('newChatModalOverlay').classList.remove('active');
    }

    function loadUsersForDropdown() {
        const select = document.getElementById('newChatUserSelect');
        select.innerHTML = '<option value="">Loading users...</option>';

        fetch('{{ route('admin.chat.search-users') }}')
            .then(res => res.json())
            .then(data => {
                if (!data.success || data.users.length === 0) {
                    select.innerHTML = '<option value="">No ERS users found</option>';
                    return;
                }

                select.innerHTML = '<option value="">-- Select a student or professor --</option>' +
                    data.users.map(u => `<option value="${u.id}" data-name="${u.name.replace(/"/g, '&quot;')}">${u.name} (${u.email})</option>`).join('');

                usersDropdownLoaded = true;
            })
            .catch(() => {
                select.innerHTML = '<option value="">Failed to load users</option>';
            });
    }

    function onUserDropdownChange(select) {
        selectedNewChatUserId = select.value || null;
        document.getElementById('startChatBtn').disabled = !selectedNewChatUserId;
    }

    function startNewChat() {
        const message = document.getElementById('newChatMessage').value.trim();
        const alertBox = document.getElementById('newChatAlert');

        if (!selectedNewChatUserId) {
            alertBox.innerHTML = '<div style="background:#fef2f2;color:#dc2626;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:10px;">Please select a user first.</div>';
            return;
        }

        if (!message) {
            alertBox.innerHTML = '<div style="background:#fef2f2;color:#dc2626;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:10px;">Please type a message to send.</div>';
            return;
        }

        const btn = document.getElementById('startChatBtn');
        btn.disabled = true;
        btn.textContent = 'Starting...';

        fetch('{{ route('admin.chat.start') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ user_id: selectedNewChatUserId, message: message }),
        })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.textContent = '🚀 Start Chat';

                if (data.success) {
                    closeNewChatModal();
                    refreshSessionsList(() => {
                        selectSession(data.session_id, data.user_name);
                    });
                } else {
                    alertBox.innerHTML = `<div style="background:#fef2f2;color:#dc2626;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:10px;">${data.message || 'Something went wrong.'}</div>`;
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = '🚀 Start Chat';
                alertBox.innerHTML = '<div style="background:#fef2f2;color:#dc2626;padding:8px 12px;border-radius:8px;font-size:12px;margin-bottom:10px;">Failed to start chat. Please try again.</div>';
            });
    }

    // ===== Real-time sidebar refresh (so new/incoming chats appear live) =====
    function refreshSessionsList(callback) {
        fetch('{{ route('admin.chat.sessions') }}')
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                const list = document.getElementById('sessionsList');
                const activeId = currentSessionId;

                if (data.sessions.length === 0) {
                    list.innerHTML = '<div class="no-chat-selected" style="padding: 20px; text-align: center;">No active chat sessions</div>';
                } else {
                    list.innerHTML = data.sessions.map(s => {
                        const isActive = s.session_id === activeId ? 'active' : '';
                        const unread = s.unread_count > 0 ? `<span class="unread-badge">${s.unread_count}</span>` : '';
                        const badge = s.started_by_admin
                            ? '<span class="started-by-admin-badge">You started</span>'
                            : (s.is_handled ? `<span class="handled-badge">${s.handled_by ? 'You' : 'Taken'}</span>` : '');

                        return `
                            <div class="session-item ${isActive}" id="session-item-${s.session_id}" onclick="selectSession(${s.session_id}, ${JSON.stringify(s.user_name)}, this)">
                                <div class="session-avatar">${s.user_name.charAt(0).toUpperCase()}</div>
                                <div class="session-info">
                                    <div class="session-user">${s.user_name}</div>
                                    <div class="session-last">${escapeHtml((s.last_message || 'No messages').substring(0, 30))}</div>
                                </div>
                                ${unread}
                                ${badge}
                            </div>
                        `;
                    }).join('');
                }

                if (typeof callback === 'function') {
                    callback();
                }
            })
            .catch(error => console.error('Sessions refresh error:', error));
    }

    function startSessionsPolling() {
        if (sessionsPollingInterval) clearInterval(sessionsPollingInterval);
        sessionsPollingInterval = setInterval(() => refreshSessionsList(), 5000);
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
        if (sessionsPollingInterval) clearInterval(sessionsPollingInterval);
    });

    document.getElementById('newChatModalOverlay').addEventListener('click', function (e) {
        if (e.target === this) {
            closeNewChatModal();
        }
    });

    // Start real-time sidebar updates
    startSessionsPolling();
</script>
@endsection