@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<style>
    .chatbot-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .chatbot-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .chatbot-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    
    .chatbot-title {
        flex: 1;
    }
    
    .chatbot-title h2 {
        margin: 0;
        font-size: 18px;
    }
    
    .chatbot-title p {
        margin: 0;
        font-size: 12px;
        opacity: 0.9;
    }
    
    .chatbot-status {
        font-size: 12px;
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
    }
    
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f7faf8;
    }
    
    .message {
        margin-bottom: 20px;
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    
    .message.user {
        justify-content: flex-end;
    }
    
    .message.bot {
        justify-content: flex-start;
    }
    
    .message-content {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.5;
        font-size: 14px;
    }
    
    .message.user .message-content {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message.bot .message-content {
        background: white;
        color: #3c4a3f;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .message-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .message.user .message-icon {
        order: 2;
        margin-right: 0;
        margin-left: 10px;
        background: #2db84f;
        color: white;
    }
    
    .message.bot .message-icon {
        background: #e8eee9;
        color: #1a7a3e;
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
        padding: 12px 16px;
        border: 1px solid #e8eee9;
        border-radius: 25px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s;
    }
    
    .chat-input:focus {
        border-color: #2db84f;
        box-shadow: 0 0 0 2px rgba(45,184,79,0.1);
    }
    
    .chat-send {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .chat-send:hover {
        transform: scale(1.02);
    }
    
    .chat-send:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
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
    
    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.5;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .quick-actions {
        padding: 15px 20px;
        background: white;
        border-top: 1px solid #e8eee9;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .quick-btn {
        background: #f0faf3;
        border: none;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
        color: #1a7a3e;
    }
    
    .quick-btn:hover {
        background: #2db84f;
        color: white;
    }
    
    .suggestion-btn {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        margin-top: 10px;
    }
    
    .suggestion-btn:hover {
        background: #1a7a3e;
    }
    
    @media (max-width: 768px) {
        .user-main {
            margin-left: 0;
        }
        .content-area {
            padding: 15px;
            padding-top: 70px;
        }
        .message-content {
            max-width: 85%;
            font-size: 13px;
        }
        .chatbot-header {
            padding: 15px;
        }
        .chatbot-icon {
            width: 40px;
            height: 40px;
            font-size: 22px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="chatbot-container">
                <div class="chatbot-header">
                    <div class="chatbot-icon">🤖</div>
                    <div class="chatbot-title">
                        <h2>UCC-ERS AI Assistant</h2>
                        <p>Your virtual assistant for venue reservations</p>
                    </div>
                    <div class="chatbot-status">
                        <span>● Online</span>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="message bot">
                        <div class="message-icon">🤖</div>
                        <div class="message-content">
                            Hello! I'm your UCC-ERS AI Assistant. 👋<br><br>
                            I can help you:
                            • Create venue reservations
                            • Check your reservation status
                            • List available venues
                            • Cancel pending reservations<br><br>
                            What would you like to do today?
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <button class="quick-btn" onclick="sendQuickMessage('Create a reservation')">📅 Create Reservation</button>
                    <button class="quick-btn" onclick="sendQuickMessage('Check my reservation status')">📊 Check Status</button>
                    <button class="quick-btn" onclick="sendQuickMessage('What venues are available?')">📍 List Venues</button>
                    <button class="quick-btn" onclick="sendQuickMessage('Help')">❓ Help</button>
                </div>

                <div class="chat-input-area">
                    <input type="text" class="chat-input" id="chatInput" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
                    <button class="chat-send" id="sendBtn" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let isProcessing = false;
    
    function handleKeyPress(event) {
        if (event.key === 'Enter' && !isProcessing) {
            sendMessage();
        }
    }
    
    function sendQuickMessage(message) {
        document.getElementById('chatInput').value = message;
        sendMessage();
    }
    
    async function sendMessage() {
        if (isProcessing) return;
        
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Add user message to chat
        addMessage(message, 'user');
        input.value = '';
        isProcessing = true;
        
        // Show typing indicator
        showTypingIndicator();
        
        try {
            const response = await fetch('{{ route("chatbot.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            // Remove typing indicator
            removeTypingIndicator();
            
            // Add bot response
            addMessage(data.message, 'bot');
            
            // Handle special actions
            if (data.action === 'redirect' && data.url) {
                setTimeout(() => {
                    addSystemMessage(`⏳ Redirecting you to the reservation page...`, 'bot');
                    setTimeout(() => {
                        window.location.href = data.url;
                    }, 1500);
                }, 500);
            } else if (data.action === 'confirm_cancel' && data.reservation_id) {
                addCancelConfirmation(data.reservation_id);
            }
            
        } catch (error) {
            console.error('Error:', error);
            removeTypingIndicator();
            addMessage('Sorry, I encountered an error. Please try again.', 'bot');
        } finally {
            isProcessing = false;
        }
    }
    
    function addMessage(text, sender) {
        const messagesDiv = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const icon = sender === 'user' ? '👤' : '🤖';
        const iconClass = sender === 'user' ? 'user' : 'bot';
        
        messageDiv.innerHTML = `
            <div class="message-icon" style="${sender === 'user' ? 'order: 2; margin-left: 10px;' : ''}">${icon}</div>
            <div class="message-content">${formatMessage(text)}</div>
        `;
        
        messagesDiv.appendChild(messageDiv);
        scrollToBottom();
    }
    
    function addSystemMessage(text, sender) {
        addMessage(text, sender);
    }
    
    function addCancelConfirmation(reservationId) {
        const messagesDiv = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot';
        messageDiv.innerHTML = `
            <div class="message-icon">🤖</div>
            <div class="message-content">
                <button class="suggestion-btn" onclick="confirmCancel(${reservationId})">✅ Yes, cancel reservation</button>
                <button class="suggestion-btn" style="background: #6e7f72;" onclick="removeMessage(this)">❌ No, keep it</button>
            </div>
        `;
        messagesDiv.appendChild(messageDiv);
        scrollToBottom();
    }
    
    function confirmCancel(reservationId) {
        fetch('{{ route("chatbot.cancel") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reservation_id: reservationId })
        })
        .then(response => response.json())
        .then(data => {
            addMessage(data.message, 'bot');
        });
    }
    
    function removeMessage(button) {
        button.closest('.message').remove();
    }
    
    function formatMessage(text) {
        // Convert markdown-style formatting
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\n/g, '<br>');
        text = text.replace(/•/g, '•');
        return text;
    }
    
    let typingIndicator = null;
    
    function showTypingIndicator() {
        const messagesDiv = document.getElementById('chatMessages');
        typingIndicator = document.createElement('div');
        typingIndicator.className = 'message bot';
        typingIndicator.id = 'typingIndicator';
        typingIndicator.innerHTML = `
            <div class="message-icon">🤖</div>
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        messagesDiv.appendChild(typingIndicator);
        scrollToBottom();
    }
    
    function removeTypingIndicator() {
        if (typingIndicator) {
            typingIndicator.remove();
            typingIndicator = null;
        }
    }
    
    function scrollToBottom() {
        const messagesDiv = document.getElementById('chatMessages');
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>
@endsection