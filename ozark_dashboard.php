<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);
if(!$user_data) {
    header("Location: login.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ozark | AI Financial Advisor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #00c4cc;
            --dark: #0f172a;
            --darker: #1e293b;
            --light: #f8fafc;
            --grey: #94a3b8;
            --success: #10b981;
            --error: #ef4444;
            --premium: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--dark);
            color: var(--light);
            height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--darker);
            border-right: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--grey);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .new-chat-btn {
            width: 100%;
            background: rgba(255,255,255,0.1);
            color: var(--light);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .new-chat-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
        }

        .chat-item {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-left: 3px solid transparent;
        }

        .chat-item:hover {
            background: rgba(255,255,255,0.05);
        }

        .chat-item.active {
            background: rgba(0,196,204,0.1);
            border-left: 3px solid var(--primary);
        }

        /* Main content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        header {
            background: var(--darker);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .logo {
            font-weight: 600;
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Chat container */
        .chat-container {
            flex: 1;
            overflow-y: auto;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* Messages */
        .message-container {
            width: 100%;
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .message-container.user {
            background: rgba(15,23,42,0.7);
        }

        .message-container.ai {
            background: rgba(15,23,42,0.4);
        }

        .message {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            gap: 1.5rem;
            padding: 0 1.5rem;
            animation: fadeIn 0.3s ease;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .user-avatar {
            background: var(--darker);
            color: var(--primary);
        }

        .ai-avatar {
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            box-shadow: 0 0 0 2px var(--primary), 0 0 10px rgba(0,196,204,0.5);
        }

        .ai-avatar::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            border-radius: 50%;
            z-index: -1;
            animation: rotate 4s linear infinite;
        }

        .ai-avatar img {
            width: 80%;
            height: 80%;
            object-fit: contain;
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.3));
        }

        .message-content {
            flex: 1;
            line-height: 1.6;
            padding-top: 0.25rem;
        }

        .message-content p {
            margin-bottom: 1rem;
        }

        .message-content p:last-child {
            margin-bottom: 0;
        }

        .message-content ul, 
        .message-content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .message-content li {
            margin-bottom: 0.5rem;
        }

        /* Input area */
        .input-area {
            padding: 1rem;
            background: var(--darker);
            position: relative;
        }

        .input-area::before {
            content: '';
            position: absolute;
            top: -1rem;
            left: 0;
            right: 0;
            height: 1rem;
            background: linear-gradient(to top, var(--darker), transparent);
        }

        .input-box {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            gap: 0.5rem;
            position: relative;
        }

        textarea {
            flex: 1;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 0.5rem;
            padding: 0.75rem 3rem 0.75rem 1rem;
            color: var(--light);
            resize: none;
            min-height: 50px;
            max-height: 200px;
            font-size: 1rem;
            line-height: 1.5;
        }

        textarea:focus {
            outline: 1px solid var(--primary);
            box-shadow: 0 0 0 3px rgba(0,196,204,0.2);
        }

        #sendBtn {
            position: absolute;
            right: 0.75rem;
            bottom: 0.75rem;
            background: transparent;
            color: var(--grey);
            border: none;
            border-radius: 0.25rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #sendBtn:not(:disabled):hover {
            color: var(--primary);
            background: rgba(0,196,204,0.1);
        }

        #sendBtn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Typing indicator */
        .typing {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            color: var(--grey);
        }

        .dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            animation: bounce 1s infinite;
        }

        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        /* Upgrade message */
        .upgrade-container {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid var(--premium);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin: 1rem auto;
            max-width: 800px;
            text-align: center;
        }

        .upgrade-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: var(--premium);
        }

        .upgrade-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .upgrade-features {
            text-align: left;
            margin: 1rem 0;
            padding: 0 1.5rem;
        }

        .upgrade-features li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .upgrade-features li::before {
            content: '✓';
            color: var(--success);
            font-weight: bold;
        }

        .upgrade-btn {
            background: linear-gradient(135deg, var(--premium), #f97316);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .message-counter {
            text-align: center;
            font-size: 0.8rem;
            color: var(--grey);
            margin-top: 0.5rem;
        }

        /* User Profile Styles */
        .user-profile {
            position: relative;
        }
        
        .profile-btn {
            background: rgba(0,196,204,0.2);
            border: 1px solid var(--primary);
            color: var(--light);
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--darker);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 10px;
            min-width: 200px;
            display: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .profile-dropdown.show {
            display: block;
        }
        
        .profile-info {
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.5rem;
        }
        
        .profile-email {
            font-size: 0.8rem;
            color: var(--grey);
            margin-top: 0.25rem;
        }
        
        .profile-menu {
            list-style: none;
        }
        
        .profile-menu li {
            margin-bottom: 0.5rem;
        }
        
        .profile-menu a {
            color: var(--light);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.2s;
        }
        
        .profile-menu a:hover {
            background: rgba(255,255,255,0.05);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                position: absolute;
                z-index: 10;
                height: 100%;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
                width: 280px;
            }
            
            .message {
                padding: 0 1rem;
                gap: 1rem;
            }
            
            .avatar {
                width: 30px;
                height: 30px;
            }
            
            .menu-btn {
                display: block;
            }
            
            .upgrade-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .upgrade-features {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">Chat History</div>
            <button class="new-chat-btn" id="newChatBtn">
                <i class="fas fa-plus"></i>
                New Chat
            </button>
        </div>
        <div class="chat-history" id="chatHistory">
            <!-- Chat history items will be added here -->
        </div>
    </div>

    <div class="main-content">
        <header>
            <button class="menu-btn" id="menuBtn" style="display: none;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">OZARK</div>
            
            <!-- User Profile Section -->
            <div class="user-profile">
                <button class="profile-btn" id="profileBtn">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($user_data['user_name']); ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-info">
                        <div class="profile-name"><?php echo htmlspecialchars($user_data['user_name']); ?></div>
                        <div class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></div>
                    </div>
                    <ul class="profile-menu">
                        <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a href="#"><i class="fas fa-star"></i> Upgrade to Premium</a></li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="chat-container" id="chatContainer">
            <!-- AI welcome message -->
            <div class="message-container ai">
                <div class="message">
                    <div class="avatar ai-avatar">
                        <img src="https://i.postimg.cc/9DP2T94b/ozark-avatar.png" alt="Ozark AI">
                    </div>
                    <div class="message-content">
                        <p>Welcome to <strong>Ozark AI</strong> - Your financial advisor powered by Mistral:</p>
                        <p>Ask me anything about finance, investments, or market trends.</p>
                        <div class="message-counter" id="messageCounter">
                            Messages remaining: 10
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-area">
            <div class="input-box">
                <textarea id="userInput" placeholder="Ask about stocks, crypto, or investments..." rows="1"></textarea>
                <button id="sendBtn" disabled><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chatContainer');
        const chatHistory = document.getElementById('chatHistory');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const newChatBtn = document.getElementById('newChatBtn');
        const sidebar = document.getElementById('sidebar');
        const menuBtn = document.getElementById('menuBtn');
        const messageCounter = document.getElementById('messageCounter');
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        
        let chats = [];
        let currentChatId = null;
        let messageCount = 0;
        const MAX_FREE_MESSAGES = 10;

        // Initialize with a new chat
        createNewChat();

        // Auto-resize textarea
        userInput.addEventListener('input', () => {
            userInput.style.height = 'auto';
            userInput.style.height = `${userInput.scrollHeight}px`;
            sendBtn.disabled = userInput.value.trim() === '' || messageCount >= MAX_FREE_MESSAGES;
        });

        // Send message on Enter (without Shift)
        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!sendBtn.disabled) sendMessage();
            }
        });

        sendBtn.addEventListener('click', sendMessage);
        newChatBtn.addEventListener('click', createNewChat);
        menuBtn.addEventListener('click', toggleSidebar);
        
        // Profile dropdown functionality
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close profile dropdown when clicking elsewhere
        document.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        // Check screen size and toggle menu button
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                menuBtn.style.display = 'block';
                sidebar.classList.remove('open');
            } else {
                menuBtn.style.display = 'none';
                sidebar.classList.add('open');
            }
        }

        window.addEventListener('resize', checkScreenSize);
        checkScreenSize();

        function toggleSidebar() {
            sidebar.classList.toggle('open');
        }

        function createNewChat() {
            if (chatContainer.children.length > 1 || userInput.value.trim() !== '') {
                if (!confirm('Start a new chat? Current conversation will be saved.')) return;
            }
            
            // Create new chat ID
            currentChatId = 'chat-' + Date.now();
            
            // Save current chat if it has messages
            if (chatContainer.children.length > 1) {
                saveCurrentChat();
            }
            
            // Clear chat container except welcome message
            while (chatContainer.children.length > 1) {
                chatContainer.removeChild(chatContainer.lastChild);
            }
            
            userInput.value = '';
            sendBtn.disabled = true;
            
            // Add to chat history
            const chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.textContent = 'New Chat ' + (chats.length + 1);
            chatItem.dataset.id = currentChatId;
            chatItem.addEventListener('click', () => loadChat(currentChatId));
            chatHistory.appendChild(chatItem);
            
            // Store chat reference
            chats.push({
                id: currentChatId,
                title: 'New Chat ' + (chats.length + 1),
                messages: []
            });
            
            // Reset message count for new chat
            messageCount = 0;
            updateMessageCounter();
            
            // Highlight current chat
            updateChatSelection();
        }

        function saveCurrentChat() {
            if (!currentChatId) return;
            
            const chat = chats.find(c => c.id === currentChatId);
            if (!chat) return;
            
            // Update messages
            chat.messages = [];
            const messageContainers = chatContainer.querySelectorAll('.message-container');
            messageContainers.forEach((container, index) => {
                if (index === 0) return; // Skip welcome message
                
                const sender = container.classList.contains('user') ? 'user' : 'ai';
                const content = container.querySelector('.message-content').textContent;
                chat.messages.push({ sender, content });
            });
            
            // Update title with first user message if available
            const firstUserMessage = chat.messages.find(m => m.sender === 'user');
            if (firstUserMessage) {
                chat.title = firstUserMessage.content.substring(0, 30) + (firstUserMessage.content.length > 30 ? '...' : '');
                const chatItem = chatHistory.querySelector(`[data-id="${currentChatId}"]`);
                if (chatItem) chatItem.textContent = chat.title;
            }
        }

        function loadChat(chatId) {
            // Save the current chat before loading another one
            saveCurrentChat();
            
            const chat = chats.find(c => c.id === chatId);
            if (!chat) return;
            
            currentChatId = chatId;
            
            // Clear chat container except welcome message
            while (chatContainer.children.length > 1) {
                chatContainer.removeChild(chatContainer.lastChild);
            }
            
            // Add messages from chat
            chat.messages.forEach(msg => {
                addMessage(msg.content, msg.sender, false);
            });
            
            // Update message count
            messageCount = chat.messages.filter(msg => msg.sender === 'user').length;
            updateMessageCounter();
            
            // Update chat selection highlight
            updateChatSelection();
            
            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
            }
        }

        function updateChatSelection() {
            const chatItems = chatHistory.querySelectorAll('.chat-item');
            chatItems.forEach(item => {
                item.classList.toggle('active', item.dataset.id === currentChatId);
            });
        }

        function updateMessageCounter() {
            const remaining = MAX_FREE_MESSAGES - messageCount;
            messageCounter.textContent = `Messages remaining: ${remaining}`;
            
            if (remaining <= 3) {
                messageCounter.style.color = remaining === 0 ? 'var(--error)' : 'var(--premium)';
            } else {
                messageCounter.style.color = 'var(--grey)';
            }
        }

        function showUpgradeMessage() {
            const containerDiv = document.createElement('div');
            containerDiv.className = 'upgrade-container';
            
            containerDiv.innerHTML = `
                <div class="upgrade-header">
                    <i class="fas fa-crown"></i>
                    <div class="upgrade-title">Upgrade to Ozark+</div>
                </div>
                <p>You've reached the free message limit. Upgrade to continue using Ozark with these benefits:</p>
                <ul class="upgrade-features">
                    <li>Unlimited messages</li>
                    <li>Advanced financial analysis</li>
                    <li>Portfolio tracking</li>
                    <li>Market alerts</li>
                    <li>Priority support</li>
                </ul>
                <button class="upgrade-btn" id="upgradeBtn">
                    Upgrade Now - €49.99/month
                </button>
            `;
            
            chatContainer.appendChild(containerDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            // Add event listener to upgrade button
            document.getElementById('upgradeBtn').addEventListener('click', () => {
                alert('Redirecting to upgrade page...');
                // In a real implementation, this would redirect to a payment page
            });
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;
            
            // Check if user has reached message limit
            if (messageCount >= MAX_FREE_MESSAGES) {
                return;
            }
            
            // Add user message
            addMessage(message, 'user');
            userInput.value = '';
            sendBtn.disabled = true;
            userInput.style.height = 'auto';
            
            // Increment message count
            messageCount++;
            updateMessageCounter();
            
            // Show typing indicator
            const typingId = showTyping();
            
            try {
                // Call Together.ai's Mistral API
                const response = await callMistralAPI(message);
                removeTyping(typingId);
                addMessage(response, 'ai');
                
                // Update chat title if it's the first message
                const chat = chats.find(c => c.id === currentChatId);
                if (chat && chat.messages.length === 0) {
                    chat.title = message.substring(0, 30) + (message.length > 30 ? '...' : '');
                    const chatItem = chatHistory.querySelector(`[data-id="${currentChatId}"]`);
                    if (chatItem) chatItem.textContent = chat.title;
                }
                
                // Check if user has reached the message limit after this exchange
                if (messageCount >= MAX_FREE_MESSAGES) {
                    showUpgradeMessage();
                    sendBtn.disabled = true;
                }
            } catch (error) {
                removeTyping(typingId);
                addMessage("Sorry, I couldn't process your request. Please try again.", 'ai');
                console.error('API Error:', error);
            }
        }

        async function callMistralAPI(prompt) {
            // Replace with your Together.ai API key and endpoint
            const API_KEY = 'my_api_key'; // Replace with your actual API key
            const MODEL = 'mistralai/Mistral-7B-Instruct-v0.1'; // Or newer version
            
            const response = await fetch('https://api.together.xyz/v1/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${API_KEY}`
                },
                body: JSON.stringify({
                    model: MODEL,
                    prompt: `You are Ozark, a financial AI assistant. Provide concise, accurate advice.\n\nUser: ${prompt}\nOzark:`,
                    max_tokens: 1000,
                    temperature: 0.7,
                    stop: ['User:', 'Ozark:']
                })
            });

            const data = await response.json();
            return data.choices?.[0]?.text?.trim() || "I couldn't generate a response.";
        }

        function addMessage(content, sender, saveToHistory = true) {
            const containerDiv = document.createElement('div');
            containerDiv.className = `message-container ${sender}`;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message';
            
            const avatarDiv = document.createElement('div');
            avatarDiv.className = `avatar ${sender}-avatar`;
            
            if (sender === 'user') {
                avatarDiv.innerHTML = '<i class="fas fa-user"></i>';
            } else {
                const img = document.createElement('img');
                img.src = 'https://i.postimg.cc/9DP2T94b/ozark-avatar.png';
                img.alt = 'Ozark AI';
                avatarDiv.appendChild(img);
            }
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = formatContent(content);
            
            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            containerDiv.appendChild(messageDiv);
            chatContainer.appendChild(containerDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            if (saveToHistory && currentChatId) {
                const chat = chats.find(c => c.id === currentChatId);
                if (chat) {
                    chat.messages.push({ sender, content });
                }
            }
        }

        function showTyping() {
            const typingId = 'typing-' + Date.now();
            const containerDiv = document.createElement('div');
            containerDiv.className = 'message-container ai';
            containerDiv.id = typingId;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message';
            
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'avatar ai-avatar';
            const img = document.createElement('img');
            img.src = 'https://i.postimg.cc/9DP2T94b/ozark-avatar.png';
            img.alt = 'Ozark AI';
            avatarDiv.appendChild(img);
            
            const contentDiv = document.createElement('div');
            contentDiv.className = 'message-content';
            contentDiv.innerHTML = `
                <div class="typing">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <span>Ozark is thinking...</span>
                </div>
            `;
            
            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            containerDiv.appendChild(messageDiv);
            chatContainer.appendChild(containerDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            return typingId;
        }

        function removeTyping(id) {
            const typingElement = document.getElementById(id);
            if (typingElement) typingElement.remove();
        }

        function formatContent(text) {
            // Simple markdown formatting
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');
        }
    </script>
</body>
</html>