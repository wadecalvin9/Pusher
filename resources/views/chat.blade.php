<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Skylex Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; overscroll-behavior: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        @keyframes slideIn { from { transform: translateX(-100%); } to { transform: translateX(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .slide-in { animation: slideIn 0.3s ease-out; }
        .fade-in { animation: fadeIn 0.2s ease-out; }
    </style>
</head>
<body class="bg-white text-gray-900 h-screen h-[100dvh] w-screen overflow-hidden flex flex-col overscroll-none">

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity"></div>

    <!-- Main App Container -->
    <div class="w-full h-full bg-white flex overflow-hidden relative">
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed md:relative inset-y-0 left-0 z-50 md:z-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-out flex flex-col w-[85vw] max-w-[380px] md:w-[380px] border-r border-gray-100 bg-white h-full shadow-2xl md:shadow-none">
            <!-- Sidebar Header -->
            <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/30">
                        S
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900">Skylex</h1>
                        <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.edit') }}" class="p-2 rounded-xl hover:bg-white/50 text-gray-600 hover:text-blue-600 transition-colors" title="Profile Settings">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl hover:bg-white/50 text-red-600 transition-colors" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                    <button id="close-sidebar" class="md:hidden p-2 rounded-xl hover:bg-white/50 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab Switcher -->
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-4 bg-gray-100 rounded-2xl p-1">
                    <button id="tab-chats" class="flex-1 text-sm font-semibold py-2.5 px-4 rounded-xl transition-all bg-white text-gray-900 shadow-sm">
                        Chats
                    </button>
                    <button id="tab-users" class="flex-1 text-sm font-medium py-2.5 px-4 rounded-xl transition-all text-gray-500 hover:text-gray-700">
                        Users
                    </button>
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversations-list" class="flex-1 overflow-y-auto px-3 py-2 space-y-1 scrollbar-hide">
                @forelse($conversations as $conversation)
                    @php
                        $otherUser = $conversation->user_one_id === auth()->id() ? $conversation->userTwo : $conversation->userOne;
                    @endphp
                    @if($otherUser)
                    <div class="conversation-item p-3 rounded-xl hover:bg-gray-50 active:bg-gray-100 cursor-pointer flex gap-3 transition-all group" 
                         data-conversation-id="{{ $conversation->id }}"
                         data-user-id="{{ $otherUser->id }}"
                         data-user-name="{{ $otherUser->name }}">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <h3 class="font-semibold text-gray-900 truncate text-sm">{{ $otherUser->name }}</h3>
                                <span class="text-xs text-gray-400">Now</span>
                            </div>
                            <p class="text-xs text-gray-500 truncate">Tap to open chat</p>
                        </div>
                    </div>
                    @else
                    <div class="p-3 rounded-xl bg-gray-50 opacity-50 flex gap-3">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 font-bold text-sm">?</div>
                        <div class="flex-1 min-w-0 flex items-center">
                            <p class="text-sm text-gray-500 italic">Unknown User</p>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="text-center py-12 px-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 mb-1">No conversations yet</p>
                        <p class="text-xs text-gray-400">Start chatting by selecting a user</p>
                    </div>
                @endforelse
            </div>

            <!-- Users List (Initially Hidden) -->
            <div id="users-list" class="hidden flex-1 overflow-y-auto px-3 py-2 space-y-1 scrollbar-hide">
                @foreach($users as $user)
                <div class="user-item p-3 rounded-xl hover:bg-gray-50 active:bg-gray-100 cursor-pointer flex gap-3 transition-all group"
                     data-user-id="{{ $user->id }}"
                     data-user-name="{{ $user->name }}">
                    <div class="relative shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 flex items-center">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-sm">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500">Tap to start chat</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Area (Main) -->
        <div class="flex-1 flex flex-col h-full bg-white relative">
            
            <!-- Chat Header -->
            <div id="chat-header" class="px-4 md:px-6 py-4 border-b border-gray-100 bg-white/80 backdrop-blur-xl sticky top-0 z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button id="open-sidebar" class="md:hidden p-2 -ml-2 rounded-xl hover:bg-gray-100 text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div id="chat-avatar" class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white font-bold shadow-md">
                        ?
                    </div>
                    <div>
                        <h2 id="chat-name" class="text-base md:text-lg font-bold text-gray-900">Select a conversation</h2>
                        <p id="chat-status" class="text-xs text-gray-400">Choose a chat from the sidebar</p>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div id="messages" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-3 scroll-smooth bg-gradient-to-b from-white to-gray-50/30">
                <div class="flex justify-center my-8">
                    <span class="text-xs text-gray-400 font-medium bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">Select a conversation to start chatting</span>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-3 md:p-4 bg-white border-t border-gray-100">
                <form id="chat-form" class="w-full flex items-center gap-2 md:gap-3">
                    <input type="hidden" id="current-conversation-id" value="">
                    
                    <div class="flex-1 relative">
                        <input type="text" 
                               id="message-input" 
                               class="w-full bg-gray-100 border-none rounded-2xl px-4 md:px-5 py-3 md:py-3.5 outline-none focus:bg-gray-50 focus:ring-2 focus:ring-blue-100 transition-all text-gray-900 placeholder-gray-400 text-sm md:text-base" 
                               placeholder="Type your message..."
                               autocomplete="off"
                               disabled>
                    </div>

                    <button type="submit" 
                            id="send-btn"
                            disabled
                            class="bg-blue-600 hover:bg-blue-700 text-white p-3 md:p-3.5 rounded-full transition-all shadow-lg shadow-blue-500/30 transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script type="module">
        const currentUserId = {{ auth()->id() }};
        const currentUserName = "{{ auth()->user()->name }}";
        let currentConversationId = null;
        let echoChannel = null;

        const messageInput = document.getElementById('message-input');
        const messagesDiv = document.getElementById('messages');
        const chatForm = document.getElementById('chat-form');
        const sendBtn = document.getElementById('send-btn');
        const chatName = document.getElementById('chat-name');
        const chatStatus = document.getElementById('chat-status');
        const chatAvatar = document.getElementById('chat-avatar');

        // Mobile sidebar controls
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('slide-in');
            sidebarOverlay.classList.remove('hidden');
            sidebarOverlay.classList.add('fade-in');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        openSidebarBtn?.addEventListener('click', openSidebar);
        closeSidebarBtn?.addEventListener('click', closeSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);

        // Tab switching
        const tabChats = document.getElementById('tab-chats');
        const tabUsers = document.getElementById('tab-users');
        const conversationsList = document.getElementById('conversations-list');
        const usersList = document.getElementById('users-list');

        tabChats.addEventListener('click', () => {
            tabChats.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            tabChats.classList.remove('text-gray-500');
            tabUsers.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            tabUsers.classList.add('text-gray-500');
            conversationsList.classList.remove('hidden');
            usersList.classList.add('hidden');
        });

        tabUsers.addEventListener('click', () => {
            tabUsers.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
            tabUsers.classList.remove('text-gray-500');
            tabChats.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
            tabChats.classList.add('text-gray-500');
            conversationsList.classList.add('hidden');
            usersList.classList.remove('hidden');
        });

        // Handle conversation click
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', async () => {
                const conversationId = item.dataset.conversationId;
                const userName = item.dataset.userName;
                const userId = item.dataset.userId;
                await loadConversation(conversationId, userName, null, userId);
                closeSidebar(); // Close sidebar on mobile after selection
            });
        });

        // Handle user click (start new conversation)
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', async () => {
                const userId = item.dataset.userId;
                const userName = item.dataset.userName;
                
                try {
                    const response = await axios.post('/conversation/get-or-create', {
                        user_id: userId
                    });
                    
                    await loadConversation(response.data.conversation.id, userName, response.data.messages);
                    closeSidebar(); // Close sidebar on mobile after selection
                } catch (error) {
                    console.error('Error creating conversation:', error);
                }
            });
        });

        async function loadConversation(conversationId, userName, existingMessages = null, userId = null) {
            currentConversationId = conversationId;
            document.getElementById('current-conversation-id').value = conversationId;
            
            // Update URL without reloading
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('conversation', conversationId);
            window.history.pushState({}, '', newUrl);

            
            // Enable input
            messageInput.disabled = false;
            sendBtn.disabled = false;
            messageInput.focus();

            // Update header
            chatName.textContent = userName;
            chatStatus.textContent = 'Online';
            chatStatus.classList.remove('text-gray-400');
            chatStatus.classList.add('text-green-500', 'font-medium');
            chatAvatar.textContent = userName.charAt(0).toUpperCase();
            chatAvatar.classList.remove('from-gray-300', 'to-gray-400');
            chatAvatar.classList.add('from-blue-500', 'to-indigo-600');

            // Clear messages
            messagesDiv.innerHTML = '<div class="flex justify-center my-8"><span class="text-xs text-gray-400 font-medium bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">Today</span></div>';

            // Load messages
            let messages = existingMessages;
            if (!messages) {
                try {
                    const response = await axios.post('/conversation/get-or-create', {
                        user_id: parseInt(userId)
                    });
                    messages = response.data.messages;
                } catch (error) {
                    console.error('Error loading messages:', error);
                    messages = [];
                }
            }

            messages.forEach(msg => {
                appendMessage(msg.user.name, msg.message, msg.user_id === currentUserId);
            });

            // Subscribe to private channel
            subscribeToConversation(conversationId);
        }

        function subscribeToConversation(conversationId) {
            if (echoChannel) {
                window.Echo.leave(`private-chat.${echoChannel}`);
            }

            echoChannel = conversationId;

            window.Echo.private(`chat.${conversationId}`)
                .listen('.MessageSent', (e) => {
                    appendMessage(e.message.user.name, e.message.message, e.message.user_id === currentUserId);
                });
        }

        // Send Message
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            
            if (!message || !currentConversationId) return;
            
            messageInput.value = '';

            try {
                await axios.post('/message/send', {
                    conversation_id: currentConversationId,
                    message: message
                });
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        function appendMessage(userName, msg, isMe) {
            const div = document.createElement('div');
            div.classList.add('fade-in');
            
            if (isMe) {
                div.className = 'flex justify-end mb-2 fade-in';
                div.innerHTML = `
                    <div class="max-w-[80%] md:max-w-[70%]">
                        <div class="bg-blue-600 text-white px-4 py-2.5 md:px-5 md:py-3 rounded-2xl rounded-tr-md shadow-md">
                            <p class="text-sm md:text-base leading-relaxed break-words">${escapeHtml(msg)}</p>
                        </div>
                        <div class="flex justify-end mt-1 items-center gap-1 px-1">
                            <span class="text-[10px] text-gray-400 font-medium">Sent</span>
                        </div>
                    </div>
                `;
            } else {
                div.className = 'flex justify-start mb-2 fade-in';
                div.innerHTML = `
                    <div class="flex items-start gap-2 md:gap-3 max-w-[80%] md:max-w-[70%]">
                        <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white shrink-0 mt-1 shadow-sm">
                             ${userName.charAt(0).toUpperCase()}
                        </div>
                        <div class="flex-1">
                            <span class="block text-[10px] text-gray-400 ml-1 mb-1 font-medium">${escapeHtml(userName)}</span>
                            <div class="bg-white border border-gray-100 text-gray-800 px-4 py-2.5 md:px-5 md:py-3 rounded-2xl rounded-tl-md shadow-sm">
                                <p class="text-sm md:text-base leading-relaxed break-words">${escapeHtml(msg)}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 ml-1 mt-1 block">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>
                `;
            }

            messagesDiv.appendChild(div);
            messagesDiv.scrollTo({ top: messagesDiv.scrollHeight, behavior: 'smooth' });
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        async function checkUrlForConversation() {
            const urlParams = new URLSearchParams(window.location.search);
            const conversationId = urlParams.get('conversation');
            
            if (conversationId) {
                // Find the conversation element to get the user name
                const conversationEl = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
                if (conversationEl) {
                    const userName = conversationEl.dataset.userName;
                    const userId = conversationEl.dataset.userId;
                    await loadConversation(conversationId, userName, null, userId);
                    // Also switch to mobile view if needed
                    if (window.innerWidth < 768) {
                        closeSidebar();
                    }
                }
            }
        }

        // Initialize
        checkUrlForConversation();

    </script>
</body>
</html>
