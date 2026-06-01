@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<style>
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .user-container {
        display: flex;
        min-height: 100vh;
        position: relative;
    }
    
    .user-main {
        flex: 1;
        margin-left: 280px;
        background: #f0faf3;
        min-height: 100vh;
        width: calc(100% - 280px);
        transition: margin-left 0.3s ease;
    }
    
    .content-area {
        padding: 20px;
    }
    
    /* Chat Container */
    .chat-container {
        display: flex;
        height: calc(100vh - 160px);
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: relative;
    }
    
    /* Sidebar */
    .chat-sidebar {
        width: 300px;
        background: #f7faf8;
        border-right: 1px solid #e8eee9;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    
    .chat-sidebar-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e8eee9;
        background: white;
    }
    
    .chat-sidebar-header h3 {
        margin: 0;
        color: #1a7a3e;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .admin-list {
        flex: 1;
        overflow-y: auto;
    }
    
    .admin-item {
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid #e8eee9;
    }
    
    .admin-item:hover {
        background: white;
    }
    
    .admin-item.active {
        background: white;
        border-left: 3px solid #2db84f;
    }
    
    .admin-avatar {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .admin-info {
        flex: 1;
        min-width: 0;
    }
    
    .admin-name {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 14px;
        margin-bottom: 2px;
    }
    
    .admin-role {
        font-size: 11px;
        color: #6e7f72;
    }
    
    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #1a7a3e;
        margin-right: 12px;
        padding: 0;
        width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .mobile-menu-toggle:hover {
        background: #f0faf3;
    }
    
    /* Main Chat Area */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        background: #f7faf8;
    }
    
    .chat-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e8eee9;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    
    .chat-header h4 {
        margin: 0;
        color: #1a7a3e;
        font-size: 15px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-shrink: 0;
    }
    
    .status-indicator {
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 20px;
        background: #f0faf3;
    }
    
    .status-active {
        color: #2db84f;
    }
    
    .status-ended {
        color: #dc2626;
    }
    
    .refresh-btn {
        background: #f0faf3;
        border: none;
        padding: 6px 12px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 11px;
        color: #1a7a3e;
        transition: all 0.2s;
    }
    
    .refresh-btn:hover {
        background: #2db84f;
        color: white;
    }
    
    /* Messages Area */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #f7faf8;
    }
    
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
        padding: 10px 14px;
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
    
    .message-attachment {
        margin-top: 8px;
    }
    
    .message-attachment a {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .message-image {
        max-width: 180px;
        max-height: 180px;
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
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Input Area */
    .chat-input-area {
        padding: 14px 16px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
        flex-shrink: 0;
        align-items: center;
    }
    
    .chat-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        outline: none;
        font-size: 13px;
        background: #f7faf8;
        transition: all 0.2s;
    }
    
    .chat-input:focus {
        border-color: #2db84f;
        background: white;
    }
    
    .chat-input:disabled {
        background: #e8eee9;
        cursor: not-allowed;
    }
    
    .file-attach-btn {
        background: #f0faf3;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    
    .file-attach-btn:hover {
        background: #2db84f;
        color: white;
    }
    
    .file-attach-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .chat-send {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }
    
    .chat-send:hover {
        transform: scale(1.05);
    }
    
    .chat-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .no-chat-selected {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #b0bdb3;
        text-align: center;
        padding: 20px;
        font-size: 13px;
    }
    
    /* Session Ended Message */
    .session-ended-message {
        background: #fef2f2;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        text-align: center;
    }
    
    .session-ended-message p {
        color: #dc2626;
        font-size: 12px;
        margin: 0;
    }
    
    /* ============================================
       MOBILE RESPONSIVE (max-width: 768px)
    ============================================ */
    @media (max-width: 1024px) {
        .user-main {
            margin-left: 0;
            width: 100%;
        }
    }
    
    @media (max-width: 768px) {
        .content-area {
            padding: 12px;
            padding-top: 60px;
        }
        
        .chat-container {
            height: calc(100vh - 120px);
            border-radius: 12px;
        }
        
        /* Sidebar - hidden by default, slides in */
        .chat-sidebar {
            position: absolute;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            z-index: 20;
            transform: translateX(-100%);
            box-shadow: none;
            border-radius: 0;
        }
        
        .chat-sidebar.open {
            transform: translateX(0);
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
        }
        
        /* Show menu toggle button */
        .mobile-menu-toggle {
            display: flex;
        }
        
        .chat-header {
            padding: 12px 16px;
        }
        
        .chat-header h4 {
            font-size: 13px;
            max-width: 150px;
        }
        
        .header-actions {
            gap: 6px;
        }
        
        .status-indicator {
            font-size: 10px;
            padding: 3px 6px;
        }
        
        .refresh-btn {
            padding: 4px 10px;
            font-size: 10px;
        }
        
        .chat-messages {
            padding: 12px;
            gap: 10px;
        }
        
        .message {
            max-width: 85%;
        }
        
        .message-bubble {
            padding: 8px 12px;
            font-size: 12px;
        }
        
        .message-image {
            max-width: 150px;
            max-height: 150px;
        }
        
        .chat-input-area {
            padding: 12px;
            gap: 8px;
        }
        
        .chat-input {
            padding: 8px 12px;
            font-size: 12px;
        }
        
        .file-attach-btn {
            width: 34px;
            height: 34px;
            font-size: 16px;
        }
        
        .chat-send {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
        
        .admin-item {
            padding: 12px 16px;
        }
        
        .admin-avatar {
            width: 38px;
            height: 38px;
            font-size: 14px;
        }
        
        .admin-name {
            font-size: 13px;
        }
        
        .admin-role {
            font-size: 10px;
        }
    }
    
    /* Small Mobile (max-width: 480px) */
    @media (max-width: 480px) {
        .content-area {
            padding: 8px;
            padding-top: 55px;
        }
        
        .chat-container {
            height: calc(100vh - 110px);
        }
        
        .chat-sidebar {
            width: 260px;
        }
        
        .chat-header h4 {
            font-size: 12px;
            max-width: 120px;
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
            font-size: 11px;
            padding: 7px 10px;
        }
        
        .chat-input-area {
            padding: 10px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="chat-container">
                <!-- Sidebar -->
                <div class="chat-sidebar" id="chatSidebar">
                    <div class="chat-sidebar-header">
                        <h3>
                            <span>💬</span> Administrators
                        </h3>
                    </div>
                    <div class="admin-list" id="adminList">
                        @forelse($admins as $admin)
                        <div class="admin-item" data-admin-id="{{ $admin->id }}" onclick="selectAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                            <div class="admin-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                            <div class="admin-info">
                                <div class="admin-name">{{ $admin->name }}</div>
                                <div class="admin-role">{{ ucfirst($admin->role) }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="no-chat-selected" style="padding: 20px; text-align: center;">No administrators available</div>
                        @endforelse
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div class="chat-main">
                    <div class="chat-header">
                        <div class="chat-header-left">
                            <button class="mobile-menu-toggle" id="menuToggle" onclick="toggleSidebar()">☰</button>
                            <h4 id="selectedAdminName">Select an admin</h4>
                        </div>
                        <div class="header-actions">
                            <div class="status-indicator" id="statusIndicator">
                                <span class="status-active">●</span> <span id="connectionStatus">Not Connected</span>
                            </div>
                            <button class="refresh-btn" onclick="refreshMessages()">⟳ Refresh</button>
                        </div>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <div class="no-chat-selected">
                            👈 Select an administrator from the left to start a conversation.
                        </div>
                    </div>
                    
                    <div class="chat-input-area" id="chatInputArea" style="display: none;">
                        <button class="file-attach-btn" id="fileAttachBtn" onclick="document.getElementById('fileInput').click()">📎</button>
                        <input type="file" id="fileInput" style="display: none;" accept="image/*,.pdf" onchange="uploadFile(this)">
                        <input type="text" class="chat-input" id="chatInput" placeholder="Type a message..." onkeypress="handleKeyPress(event)">
                        <button class="chat-send" id="sendBtn" onclick="sendMessage()">➤</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let currentAdminId = null;
    let currentAdminName = null;
    let lastMessageId = 0;
    let pollingInterval = null;
    let messageIds = new Set();
    let currentSessionActive = false;
    
    // Check if mobile
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    function toggleSidebar() {
        const sidebar = document.getElementById('chatSidebar');
        sidebar.classList.toggle('open');
    }
    
    function closeSidebar() {
        if (isMobile()) {
            const sidebar = document.getElementById('chatSidebar');
            sidebar.classList.remove('open');
        }
    }
    
    function selectAdmin(adminId, adminName) {
        if (currentAdminId === adminId) return;
        
        currentAdminId = adminId;
        currentAdminName = adminName;
        lastMessageId = 0;
        messageIds.clear();
        currentSessionActive = true; // Reset to true for new admin
        
        const nameElement = document.getElementById('selectedAdminName');
        if (nameElement) {
            nameElement.innerText = adminName;
        }
        
        const chatInputArea = document.getElementById('chatInputArea');
        if (chatInputArea) {
            chatInputArea.style.display = 'flex';
        }
        
        // Clear messages
        const messagesDiv = document.getElementById('chatMessages');
        if (messagesDiv) {
            messagesDiv.innerHTML = '';
        }
        
        // Remove any session ended message
        const endedMsg = document.getElementById('sessionEndedMessage');
        if (endedMsg) {
            endedMsg.remove();
        }
        
        // Enable inputs
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const fileAttachBtn = document.getElementById('fileAttachBtn');
        if (chatInput) chatInput.disabled = false;
        if (sendBtn) sendBtn.disabled = false;
        if (fileAttachBtn) fileAttachBtn.disabled = false;
        
        // Update status indicator
        const statusIndicator = document.getElementById('statusIndicator');
        const connectionStatus = document.getElementById('connectionStatus');
        if (statusIndicator && connectionStatus) {
            statusIndicator.innerHTML = '<span class="status-active">●</span>';
            connectionStatus.innerText = 'Active';
        }
        
        loadMessages();
        startPolling();
        
        // Update active state
        document.querySelectorAll('.admin-item').forEach(item => {
            item.classList.remove('active');
        });
        const selectedItem = document.querySelector(`.admin-item[data-admin-id="${adminId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active');
        }
        
        // Close sidebar on mobile after selection
        closeSidebar();
    }
    
    function loadMessages() {
        if (!currentAdminId) return;
        
        fetch(`/user/chat/messages/${currentAdminId}?last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.messages && data.messages.length > 0) {
                        appendNewMessages(data.messages);
                        if (data.latest_id) {
                            lastMessageId = data.latest_id;
                        }
                    }
                    // Session is always active for this admin
                    currentSessionActive = true;
                    updateSessionUI();
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    function updateSessionUI() {
        const statusIndicator = document.getElementById('statusIndicator');
        const connectionStatus = document.getElementById('connectionStatus');
        const chatInput = document.getElementById('chatInput');
        const sendBtn = document.getElementById('sendBtn');
        const fileAttachBtn = document.getElementById('fileAttachBtn');
        
        // Always enable for the selected admin
        if (statusIndicator && connectionStatus) {
            statusIndicator.innerHTML = '<span class="status-active">●</span>';
            connectionStatus.innerText = 'Active';
        }
        
        if (chatInput) chatInput.disabled = false;
        if (sendBtn) sendBtn.disabled = false;
        if (fileAttachBtn) fileAttachBtn.disabled = false;
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
    
    function refreshMessages() {
        if (currentAdminId) {
            loadMessages();
        }
    }
    
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message || !currentAdminId) return;
        
        if (!currentSessionActive) {
            alert('This chat session has ended. Please start a new chat by saying "talk to admin" in the AI Assistant.');
            return;
        }
        
        addMessageToUI(message, true);
        input.value = '';
        
        const formData = new FormData();
        formData.append('message', message);
        formData.append('receiver_id', currentAdminId);
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
            } else if (data.message) {
                addMessageToUI('❌ ' + data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addMessageToUI('❌ Failed to send', false);
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
        if (!file || !currentAdminId) return;
        
        if (!currentSessionActive) {
            alert('This chat session has ended. Please start a new chat by saying "talk to admin" in the AI Assistant.');
            input.value = '';
            return;
        }
        
        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('receiver_id', currentAdminId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('message', '');
        
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
        pollingInterval = setInterval(() => {
            if (currentAdminId) {
                loadMessages();
            }
        }, 3000);
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
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (isMobile()) {
            const sidebar = document.getElementById('chatSidebar');
            const toggle = document.getElementById('menuToggle');
            
            if (sidebar && toggle && sidebar.classList.contains('open')) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            const sidebar = document.getElementById('chatSidebar');
            if (sidebar) {
                sidebar.classList.remove('open');
            }
        }
    });
    
    // Clean up on unload
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) clearInterval(pollingInterval);
    });
    
    // Auto-select first admin on load
    document.addEventListener('DOMContentLoaded', function() {
        const firstAdmin = document.querySelector('.admin-item');
        if (firstAdmin) {
            const adminId = firstAdmin.getAttribute('data-admin-id');
            const adminName = firstAdmin.querySelector('.admin-name')?.innerText || 'Admin';
            if (adminId) {
                setTimeout(() => {
                    selectAdmin(parseInt(adminId), adminName);
                }, 100);
            }
        }
    });
</script>
@endsection