/**
 * Floating Chat System - Main Module
 * A Facebook/LinkedIn-style floating chat interface
 */

class FloatingChatSystem {
    constructor() {
        this.isInitialized = false;
        this.conversations = [];
        this.openWindows = new Map();
        this.unreadCount = 0;
        this.pollInterval = null;
        this.typingTimeouts = new Map();
        
        this.init();
    }

    async init() {
        if (this.isInitialized) return;
        
        try {
            // Create main chat elements
            this.createChatButton();
            this.createChatSidebar();
            
            // Load initial data
            await this.loadConversations();
            await this.updateUnreadCount();
            
            // Set online status
            await this.setOnlineStatus();
            
            // Start polling for updates
            this.startPolling();
            
            // Setup visibility change handler
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.setOnlineStatus();
                }
            });
            
            this.isInitialized = true;
            console.log('✅ Floating Chat System initialized');
        } catch (error) {
            console.error('❌ Failed to initialize chat system:', error);
        }
    }

    createChatButton() {
        const button = document.createElement('button');
        button.id = 'floating-chat-button';
        button.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span id="chat-badge" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
        `;
        button.className = 'fixed bottom-6 right-6 bg-green-600 hover:bg-green-700 text-white rounded-full p-4 shadow-lg z-50 transition-all duration-300 hover:scale-110';
        button.onclick = () => this.toggleSidebar();
        
        document.body.appendChild(button);
    }

    createChatSidebar() {
        const sidebar = document.createElement('div');
        sidebar.id = 'floating-chat-sidebar';
        sidebar.className = 'fixed bottom-0 right-6 w-80 bg-white dark:bg-gray-800 rounded-t-lg shadow-2xl z-40 transform translate-y-full transition-transform duration-300';
        sidebar.innerHTML = `
            <div class="flex flex-col h-[600px]">
                <!-- Header -->
                <div class="bg-green-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                    <h3 class="font-semibold text-lg">Messages</h3>
                    <button onclick="window.floatingChat.closeSidebar()" class="hover:bg-green-700 rounded-full p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Search Bar -->
                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="chat-user-search"
                            placeholder="Search users..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white"
                        />
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div id="chat-search-results" class="hidden absolute bg-white dark:bg-gray-700 w-72 mt-2 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50"></div>
                </div>
                
                <!-- Conversations List -->
                <div id="chat-conversations-list" class="flex-1 overflow-y-auto">
                    <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p>No conversations yet</p>
                            <p class="text-sm mt-2">Search for users to start chatting</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(sidebar);
        
        // Setup search
        const searchInput = document.getElementById('chat-user-search');
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.searchUsers(e.target.value), 300);
        });
    }

    toggleSidebar() {
        const sidebar = document.getElementById('floating-chat-sidebar');
        const isOpen = !sidebar.classList.contains('translate-y-full');
        
        if (isOpen) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    }

    openSidebar() {
        const sidebar = document.getElementById('floating-chat-sidebar');
        sidebar.classList.remove('translate-y-full');
        this.loadConversations();
    }

    closeSidebar() {
        const sidebar = document.getElementById('floating-chat-sidebar');
        sidebar.classList.add('translate-y-full');
        document.getElementById('chat-search-results').classList.add('hidden');
    }

    async loadConversations() {
        try {
            const response = await fetch('/chat/conversations', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            
            if (!response.ok) throw new Error('Failed to load conversations');
            
            const data = await response.json();
            this.conversations = data.conversations;
            this.renderConversations();
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    renderConversations() {
        const container = document.getElementById('chat-conversations-list');
        
        if (this.conversations.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>No conversations yet</p>
                        <p class="text-sm mt-2">Search for users to start chatting</p>
                    </div>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.conversations.map(conv => `
            <div class="conversation-item border-b border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                 onclick="window.floatingChat.openChatWindow(${conv.id}, ${JSON.stringify(conv.user).replace(/"/g, '&quot;')})">
                <div class="flex items-start space-x-3">
                    <div class="relative flex-shrink-0">
                        <img src="${conv.user.avatar}" alt="${conv.user.name}" class="w-12 h-12 rounded-full object-cover">
                        ${conv.user.is_online ? '<span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>' : ''}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold text-gray-900 dark:text-white truncate">${conv.user.name}</h4>
                            ${conv.last_message ? `<span class="text-xs text-gray-500 dark:text-gray-400">${this.formatTime(conv.last_message.created_at)}</span>` : ''}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                            ${conv.last_message ? (conv.last_message.sender_id === window.Laravel.user.id ? 'You: ' : '') + conv.last_message.message : 'Start a conversation'}
                        </p>
                        ${conv.unread_count > 0 ? `<span class="inline-block mt-1 bg-green-600 text-white text-xs px-2 py-0.5 rounded-full">${conv.unread_count}</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    async searchUsers(query) {
        const resultsDiv = document.getElementById('chat-search-results');
        
        if (!query || query.trim().length < 1) {
            resultsDiv.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`/chat/users/search?query=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            
            if (!response.ok) throw new Error('Search failed');
            
            const data = await response.json();
            
            if (data.users.length === 0) {
                resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400">No users found</div>';
            } else {
                resultsDiv.innerHTML = data.users.map(user => `
                    <div class="p-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center space-x-3 transition-colors"
                         onclick="window.floatingChat.startConversation(${user.id}, ${JSON.stringify(user).replace(/"/g, '&quot;')})">
                        <div class="relative flex-shrink-0">
                            <img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover">
                            ${user.is_online ? '<span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-700 rounded-full"></span>' : ''}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">${user.name}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${user.email}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full ${this.getRoleBadgeClass(user.role)}">${user.role}</span>
                    </div>
                `).join('');
            }
            
            resultsDiv.classList.remove('hidden');
        } catch (error) {
            console.error('Error searching users:', error);
            resultsDiv.innerHTML = '<div class="p-4 text-center text-red-500">Error searching users</div>';
            resultsDiv.classList.remove('hidden');
        }
    }

    async startConversation(userId, user) {
        document.getElementById('chat-search-results').classList.add('hidden');
        document.getElementById('chat-user-search').value = '';
        
        try {
            const response = await fetch('/chat/conversations/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ user_id: userId }),
            });
            
            if (!response.ok) throw new Error('Failed to start conversation');
            
            const data = await response.json();
            await this.loadConversations();
            this.openChatWindow(data.conversation.id, data.conversation.user);
            this.closeSidebar();
        } catch (error) {
            console.error('Error starting conversation:', error);
            alert('Failed to start conversation');
        }
    }

    openChatWindow(conversationId, user) {
        if (this.openWindows.has(conversationId)) {
            const window = this.openWindows.get(conversationId);
            window.element.classList.remove('minimized');
            return;
        }
        
        const chatWindow = new ChatWindow(conversationId, user, this);
        this.openWindows.set(conversationId, chatWindow);
        this.repositionWindows();
    }

    closeChatWindow(conversationId) {
        if (this.openWindows.has(conversationId)) {
            const window = this.openWindows.get(conversationId);
            window.destroy();
            this.openWindows.delete(conversationId);
            this.repositionWindows();
        }
    }

    repositionWindows() {
        const windows = Array.from(this.openWindows.values());
        const windowWidth = 350;
        const windowGap = 10;
        const rightOffset = 24;
        
        windows.forEach((window, index) => {
            const right = rightOffset + (index * (windowWidth + windowGap));
            window.element.style.right = `${right}px`;
        });
    }

    async updateUnreadCount() {
        try {
            const response = await fetch('/chat/unread-count', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            
            if (!response.ok) throw new Error('Failed to get unread count');
            
            const data = await response.json();
            this.unreadCount = data.unread_count;
            
            const badge = document.getElementById('chat-badge');
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error updating unread count:', error);
        }
    }

    async setOnlineStatus() {
        try {
            await fetch('/chat/online', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
        } catch (error) {
            console.error('Error setting online status:', error);
        }
    }

    startPolling() {
        // Poll every 5 seconds for updates
        this.pollInterval = setInterval(() => {
            this.updateUnreadCount();
            
            // Update open windows
            this.openWindows.forEach(window => {
                window.pollMessages();
                window.checkTypingStatus();
            });
        }, 5000);
    }

    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
        
        return date.toLocaleDateString();
    }

    getRoleBadgeClass(role) {
        const classes = {
            'admin': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'professor': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'student': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        };
        return classes[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    destroy() {
        this.stopPolling();
        this.openWindows.forEach(window => window.destroy());
        this.openWindows.clear();
        
        document.getElementById('floating-chat-button')?.remove();
        document.getElementById('floating-chat-sidebar')?.remove();
        
        this.isInitialized = false;
    }
}

// Export to global scope
window.FloatingChatSystem = FloatingChatSystem;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('🔍 Checking chat initialization requirements...');
    console.log('- window.Laravel exists:', !!window.Laravel);
    console.log('- window.Laravel.user exists:', !!(window.Laravel && window.Laravel.user));
    
    if (window.Laravel && window.Laravel.user) {
        console.log('✅ Initializing chat for user:', window.Laravel.user.name);
        window.floatingChat = new FloatingChatSystem();
    } else {
        console.log('⚠️ Chat not initialized: User not authenticated or Laravel object missing');
    }
});

// Also try to initialize after a short delay (in case DOM loads before script)
setTimeout(() => {
    if (!window.floatingChat && window.Laravel && window.Laravel.user) {
        console.log('🔄 Retrying chat initialization...');
        window.floatingChat = new FloatingChatSystem();
    }
}, 500);
