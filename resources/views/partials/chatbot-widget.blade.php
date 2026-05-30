<style>
    .chatbot-widget {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1000;
        font-family: 'Arial', sans-serif;
    }

    .chatbot-toggle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        position: relative;
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
    }

    .chatbot-icon {
        font-size: 28px;
        color: white;
    }

    .notification-dot {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 15px;
        height: 15px;
        background: #dc2626;
        border-radius: 50%;
        border: 2px solid white;
        display: none;
    }

    .chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 400px;
        height: 600px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: slideUp 0.3s ease;
        z-index: 1000;
    }

    .chatbot-window.open {
        display: flex;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatbot-window-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chatbot-window-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .chatbot-window-title {
        flex: 1;
    }

    .chatbot-window-title h4 {
        margin: 0;
        font-size: 16px;
    }

    .chatbot-window-title p {
        margin: 0;
        font-size: 11px;
        opacity: 0.9;
    }

    .chatbot-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        background: #f7faf8;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-message {
        display: flex;
        animation: fadeIn 0.3s ease;
        max-width: 85%;
    }

    .chat-message.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .chat-message.bot {
        align-self: flex-start;
    }

    .chat-message-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .chat-message.user .chat-message-avatar {
        background: #2db84f;
        margin-left: 8px;
    }

    .chat-message.bot .chat-message-avatar {
        background: #e8eee9;
        margin-right: 8px;
    }

    .chat-message-bubble {
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .chat-message.user .chat-message-bubble {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .chat-message.bot .chat-message-bubble {
        background: white;
        color: #3c4a3f;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 10px 14px;
        background: white;
        border-radius: 18px;
        width: fit-content;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        background: #b0bdb3;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30% { transform: translateY(-8px); opacity: 1; }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chatbot-quick-actions {
        padding: 10px 15px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        background: #f0faf3;
        border: none;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.3s;
        color: #1a7a3e;
    }

    .quick-action-btn:hover {
        background: #2db84f;
        color: white;
    }

    .chatbot-input-area {
        padding: 15px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .chatbot-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        font-size: 13px;
        outline: none;
        transition: all 0.3s;
    }

    .chatbot-input:focus {
        border-color: #2db84f;
        box-shadow: 0 0 0 2px rgba(45,184,79,0.1);
    }

    .file-attach-btn {
        background: #f0faf3;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s;
    }

    .file-attach-btn:hover {
        background: #2db84f;
        color: white;
    }

    .chatbot-send {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .chatbot-send:hover {
        transform: scale(1.05);
    }

    .chatbot-send:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .suggestion-btn {
        background: #2db84f;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
        margin-top: 8px;
        margin-right: 8px;
    }

    .suggestion-btn:hover {
        background: #1a7a3e;
    }

    .file-list {
        padding: 10px 15px;
        background: #f7faf8;
        border-top: 1px solid #e8eee9;
        max-height: 150px;
        overflow-y: auto;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 10px;
        background: white;
        border-radius: 8px;
        margin-bottom: 5px;
        font-size: 11px;
    }

    .remove-file-btn {
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 2px 8px;
        cursor: pointer;
        font-size: 10px;
    }

    @media (max-width: 768px) {
        .chatbot-window {
            width: calc(100vw - 40px);
            right: 20px;
            left: 20px;
            bottom: 80px;
            height: 500px;
        }
        .chatbot-toggle {
            width: 50px;
            height: 50px;
        }
        .chatbot-icon {
            font-size: 24px;
        }
    }
</style>

<div class="chatbot-widget">
    <div class="chatbot-toggle" onclick="toggleChatbot()">
        <span class="chatbot-icon">🤖</span>
        <span class="notification-dot" id="chatbotNotification"></span>
    </div>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-window-header">
            <div class="chatbot-window-icon">🤖</div>
            <div class="chatbot-window-title">
                <h4>UCC-ERS AI Assistant</h4>
                <p>Online • Ready to help</p>
            </div>
            <button class="chatbot-close" onclick="toggleChatbot()">&times;</button>
        </div>

        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chat-message bot">
                <div class="chat-message-avatar">🤖</div>
                <div class="chat-message-bubble">
                    <strong>👋 Welcome to UCC-ERS AI Assistant!</strong><br><br>
                    
                    I'm here to help you manage your venue reservations.<br><br>
                    
                    <strong>✨ What I can do:</strong><br>
                    • 📅 <strong>Create reservations</strong> - Single or multiple dates<br>
                    • 📊 <strong>Check status</strong> - View your bookings<br>
                    • 📍 <strong>List venues</strong> - See all available venues<br>
                    • 📎 <strong>Upload files</strong> - PDF, JPG, PNG (max 15MB)<br>
                    • ❌ <strong>Cancel reservations</strong> - Cancel pending bookings<br><br>
                    
                    <strong>🚀 Quick Commands:</strong><br>
                    • Type <strong>"create reservation"</strong> to start booking<br>
                    • Type <strong>"my reservation status"</strong> to check your bookings<br>
                    • Type <strong>"list venues"</strong> to see available venues<br>
                    • Type <strong>"help"</strong> for all commands<br><br>
                    
                    <strong>💬 How can I help you today?</strong>
                </div>
            </div>
        </div>

        <div class="chatbot-quick-actions">
            <button class="quick-action-btn" onclick="sendQuickMessage('create reservation')">📅 New Reservation</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('my reservation status')">📊 Check Status</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('list venues')">📍 List Venues</button>
            <button class="quick-action-btn" onclick="sendQuickMessage('help')">❓ Help</button>
        </div>

        <div id="fileListContainer" class="file-list" style="display: none;"></div>

        <div class="chatbot-input-area">
            <button class="file-attach-btn" onclick="triggerFileUpload()" title="Attach files">📎</button>
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message..." onkeypress="handleChatbotKeyPress(event)">
            <button class="chatbot-send" onclick="sendChatbotMessage()">➤</button>
        </div>

        <input type="file" id="chatbotFileInput" style="display: none;" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelection(this)">
    </div>
</div>

<script>
    let isChatbotProcessing = false;
    let chatbotTypingIndicator = null;
    let isProcessingUpload = false;
    let attachedFiles = [];

    function toggleChatbot() {
        const window = document.getElementById('chatbotWindow');
        window.classList.toggle('open');
        if (window.classList.contains('open')) {
            document.getElementById('chatbotInput').focus();
        }
    }

    function handleChatbotKeyPress(event) {
        if (event.key === 'Enter' && !isChatbotProcessing) {
            sendChatbotMessage();
        }
    }

    function sendQuickMessage(message) {
        document.getElementById('chatbotInput').value = message;
        sendChatbotMessage();
    }

    function triggerFileUpload() {
        document.getElementById('chatbotFileInput').click();
    }

    function handleFileSelection(input) {
        if (isProcessingUpload) {
            alert('Please wait, files are being uploaded...');
            return;
        }
        
        const files = Array.from(input.files);
        const maxSize = 15 * 1024 * 1024;
        const maxFiles = 5;
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        
        if (attachedFiles.length + files.length > maxFiles) {
            alert(`Maximum ${maxFiles} files allowed. You already have ${attachedFiles.length} file(s).`);
            input.value = '';
            return;
        }
        
        // Validate files
        for (let file of files) {
            if (!allowedTypes.includes(file.type)) {
                alert(`Invalid file type: ${file.name}. Only PDF, JPG, PNG allowed.`);
                input.value = '';
                return;
            }
            if (file.size > maxSize) {
                alert(`File too large: ${file.name}. Maximum size is 15MB.`);
                input.value = '';
                return;
            }
            // Check for duplicate by name
            if (attachedFiles.some(f => f.name === file.name && f.size === file.size)) {
                alert(`Duplicate file: ${file.name} is already attached.`);
                input.value = '';
                return;
            }
            attachedFiles.push(file);
        }
        
        displayFileList();
        input.value = '';
        
        if (files.length > 0) {
            addChatbotMessage(`📎 Attached ${files.length} file(s)`, 'user');
            uploadFilesToServer();
        }
    }

    async function uploadFilesToServer() {
        if (attachedFiles.length === 0) return;
        if (isProcessingUpload) return;
        
        isProcessingUpload = true;
        
        const formData = new FormData();
        for (let i = 0; i < attachedFiles.length; i++) {
            formData.append('attachments[]', attachedFiles[i]);
        }
        
        try {
            const response = await fetch('{{ route("chatbot.process") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            if (data.message) {
                addChatbotMessage(data.message, 'bot');
            }
        } catch (error) {
            console.error('Upload error:', error);
            addChatbotMessage('Sorry, there was an error uploading your files. Please try again.', 'bot');
        } finally {
            isProcessingUpload = false;
        }
    }

    function removeFile(index) {
        attachedFiles.splice(index, 1);
        displayFileList();
    }

    function displayFileList() {
        const container = document.getElementById('fileListContainer');
        
        if (attachedFiles.length === 0) {
            container.style.display = 'none';
            container.innerHTML = '';
            return;
        }
        
        container.style.display = 'block';
        let html = '<strong>📎 Attached Files:</strong>';
        attachedFiles.forEach((file, index) => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            html += `
                <div class="file-item">
                    <span>📄 ${file.name} (${size} MB)</span>
                    <button class="remove-file-btn" onclick="removeFile(${index})">Remove</button>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    async function sendChatbotMessage() {
        if (isChatbotProcessing) return;
        
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();
        
        if (!message && attachedFiles.length === 0) return;
        
        if (message) {
            addChatbotMessage(message, 'user');
        }
        
        input.value = '';
        isChatbotProcessing = true;
        
        showChatbotTyping();
        
        const formData = new FormData();
        formData.append('message', message);
        
        // DO NOT append files here - they are already uploaded separately
        // Only send the message text
        
        try {
            const response = await fetch('{{ route("chatbot.process") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const data = await response.json();
            removeChatbotTyping();
            addChatbotMessage(data.message, 'bot');
            
            if (data.intent === 'reservation_created') {
                // Clear files after successful submission
                attachedFiles = [];
                displayFileList();
            }
            
        } catch (error) {
            console.error('Error:', error);
            removeChatbotTyping();
            addChatbotMessage('Sorry, I encountered an error. Please try again.', 'bot');
        } finally {
            isChatbotProcessing = false;
        }
    }

    function addChatbotMessage(text, sender) {
        const messagesDiv = document.getElementById('chatbotMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        
        const avatar = sender === 'user' ? '👤' : '🤖';
        
        messageDiv.innerHTML = `
            <div class="chat-message-avatar">${avatar}</div>
            <div class="chat-message-bubble">${formatChatbotMessage(text)}</div>
        `;
        
        messagesDiv.appendChild(messageDiv);
        scrollChatbotToBottom();
    }

    function formatChatbotMessage(text) {
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        text = text.replace(/•/g, '•');
        return text;
    }

    function showChatbotTyping() {
        const messagesDiv = document.getElementById('chatbotMessages');
        chatbotTypingIndicator = document.createElement('div');
        chatbotTypingIndicator.className = 'chat-message bot';
        chatbotTypingIndicator.id = 'chatbotTyping';
        chatbotTypingIndicator.innerHTML = `
            <div class="chat-message-avatar">🤖</div>
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        messagesDiv.appendChild(chatbotTypingIndicator);
        scrollChatbotToBottom();
    }

    function removeChatbotTyping() {
        if (chatbotTypingIndicator) {
            chatbotTypingIndicator.remove();
            chatbotTypingIndicator = null;
        }
    }

    function scrollChatbotToBottom() {
        const messagesDiv = document.getElementById('chatbotMessages');
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>