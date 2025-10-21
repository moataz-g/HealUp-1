/**
 * Chat Window Component
 * Individual floating chat window for conversations
 */

class ChatWindow {
    constructor(conversationId, user, chatSystem) {
        this.conversationId = conversationId;
        this.user = user;
        this.chatSystem = chatSystem;
        this.messages = [];
        this.isMinimized = false;
        this.isTyping = false;
        this.typingTimeout = null;
        this.otherUserTyping = false;

        this.createElement();
        this.setupEventListeners();
        this.loadMessages();
        this.makeDraggable();
        this.makeResizable();
    }

    createElement() {
        const window = document.createElement('div');
        window.id = `chat-window-${this.conversationId}`;
        window.className = 'chat-window fixed bottom-0 w-[350px] bg-white dark:bg-gray-800 rounded-t-lg shadow-2xl z-30 flex flex-col transition-all duration-300';
        window.style.height = '500px';

        window.innerHTML = `
            <!-- Header -->
            <div class="chat-window-header bg-gradient-to-r from-green-600 to-green-700 text-white p-3 rounded-t-lg cursor-move flex items-center justify-between">
                <div class="flex items-center space-x-2 flex-1 min-w-0">
                    <div class="relative flex-shrink-0">
                        <img src="${this.user.avatar}" alt="${this.user.name}" class="w-8 h-8 rounded-full object-cover border-2 border-white">
                        ${this.user.is_online ? '<span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-green-700 rounded-full"></span>' : ''}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-sm truncate">${this.user.name}</h4>
                        <p class="text-xs opacity-90" id="typing-indicator-${this.conversationId}">
                            ${this.user.is_online ? '<span class="text-green-300">● Online</span>' : 'Offline'}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button onclick="window.floatingChat.openWindows.get(${this.conversationId}).minimize()"
                            class="hover:bg-green-600 rounded p-1.5 transition-colors"
                            title="Minimize">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <button onclick="window.floatingChat.closeChatWindow(${this.conversationId})"
                            class="hover:bg-green-600 rounded p-1.5 transition-colors"
                            title="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="chat-window-body flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900" id="messages-${this.conversationId}">
                <div class="flex items-center justify-center h-full">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="chat-window-footer border-t border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-800">
                <form onsubmit="window.floatingChat.openWindows.get(${this.conversationId}).sendMessage(event)" class="flex items-end space-x-2">
                    <textarea
                        id="message-input-${this.conversationId}"
                        placeholder="Type a message..."
                        rows="1"
                        class="flex-1 resize-none border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white max-h-24 overflow-y-auto"
                        onkeydown="window.floatingChat.openWindows.get(${this.conversationId}).handleKeyDown(event)"
                        oninput="window.floatingChat.openWindows.get(${this.conversationId}).handleTyping()"
                    ></textarea>
                    <button
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white rounded-lg px-4 py-2 transition-colors flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed"
                        id="send-btn-${this.conversationId}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Resize Handle -->
            <div class="resize-handle absolute top-0 left-0 w-full h-2 cursor-ns-resize"></div>
        `;

        document.body.appendChild(window);
        this.element = window;
    }

    setupEventListeners() {
        // Auto-resize textarea
        const textarea = document.getElementById(`message-input-${this.conversationId}`);
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 96) + 'px';
        });
    }

    async loadMessages() {
        try {
            const response = await fetch(`/chat/conversations/${this.conversationId}/messages`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Failed to load messages');

            const data = await response.json();
            this.messages = data.messages;
            this.renderMessages();
            this.scrollToBottom();

            // Mark as read
            await this.markAsRead();
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Failed to load messages');
        }
    }

    renderMessages() {
        const container = document.getElementById(`messages-${this.conversationId}`);
        const currentUserId = window.Laravel.user.id;

        if (this.messages.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm">No messages yet</p>
                        <p class="text-xs mt-1">Start the conversation!</p>
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = this.messages.map((msg, index) => {
            const isMine = msg.sender_id === currentUserId;
            const showAvatar = !isMine && (index === 0 || this.messages[index - 1].sender_id !== msg.sender_id);
            const showName = !isMine && showAvatar;

            return `
                <div class="flex ${isMine ? 'justify-end' : 'justify-start'} items-end space-x-2">
                    ${!isMine ? `
                        <div class="flex-shrink-0 w-8">
                            ${showAvatar ? `<img src="${msg.sender.avatar}" alt="${msg.sender.name}" class="w-8 h-8 rounded-full object-cover">` : ''}
                        </div>
                    ` : ''}
                    <div class="flex flex-col ${isMine ? 'items-end' : 'items-start'} max-w-[70%]">
                        ${showName ? `<span class="text-xs text-gray-600 dark:text-gray-400 mb-1 px-2">${msg.sender.name}</span>` : ''}
                        <div class="relative group">
                            <div class="${isMine ? 'bg-green-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white'} rounded-2xl px-4 py-2 shadow-sm">
                                <p class="text-sm whitespace-pre-wrap break-words">${this.escapeHtml(msg.message)}</p>
                            </div>
                            <div class="flex items-center space-x-1 mt-1 px-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">${this.formatMessageTime(msg.created_at)}</span>
                                ${isMine && msg.is_read ? '<svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>' : ''}
                            </div>
                        </div>
                    </div>
                    ${isMine ? '<div class="flex-shrink-0 w-8"></div>' : ''}
                </div>
            `;
        }).join('');

        // Add typing indicator if other user is typing
        if (this.otherUserTyping) {
            container.innerHTML += `
                <div class="flex justify-start items-end space-x-2">
                    <div class="flex-shrink-0 w-8">
                        <img src="${this.user.avatar}" alt="${this.user.name}" class="w-8 h-8 rounded-full object-cover">
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-2xl px-4 py-3 shadow-sm">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    async sendMessage(event) {
        event.preventDefault();

        const input = document.getElementById(`message-input-${this.conversationId}`);
        const message = input.value.trim();

        if (!message) return;

        const sendBtn = document.getElementById(`send-btn-${this.conversationId}`);
        sendBtn.disabled = true;

        try {
            const response = await fetch(`/chat/conversations/${this.conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message }),
            });

            if (!response.ok) throw new Error('Failed to send message');

            const data = await response.json();
            this.messages.push(data.message);
            this.renderMessages();
            this.scrollToBottom();

            input.value = '';
            input.style.height = 'auto';

            // Update conversations list
            this.chatSystem.loadConversations();
            this.chatSystem.updateUnreadCount();
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Failed to send message');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    async pollMessages() {
        if (this.isMinimized) return;

        try {
            const response = await fetch(`/chat/conversations/${this.conversationId}/messages`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.messages.length > this.messages.length) {
                this.messages = data.messages;
                this.renderMessages();
                this.scrollToBottom();
                await this.markAsRead();
                this.chatSystem.updateUnreadCount();
            }
        } catch (error) {
            // Silently fail for polling
        }
    }

    handleKeyDown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            this.sendMessage(event);
        }
    }

    handleTyping() {
        if (!this.isTyping) {
            this.isTyping = true;
            this.updateTypingStatus(true);
        }

        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
            this.updateTypingStatus(false);
        }, 3000);
    }

    async updateTypingStatus(isTyping) {
        try {
            await fetch(`/chat/conversations/${this.conversationId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ is_typing: isTyping }),
            });
        } catch (error) {
            // Silently fail
        }
    }

    async checkTypingStatus() {
        if (this.isMinimized) return;

        try {
            const response = await fetch(`/chat/conversations/${this.conversationId}/typing`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.is_typing !== this.otherUserTyping) {
                this.otherUserTyping = data.is_typing;

                const indicator = document.getElementById(`typing-indicator-${this.conversationId}`);
                if (this.otherUserTyping) {
                    indicator.innerHTML = '<span class="text-green-300">typing...</span>';
                    this.renderMessages();
                    this.scrollToBottom();
                } else {
                    indicator.innerHTML = this.user.is_online ? '<span class="text-green-300">● Online</span>' : 'Offline';
                    this.renderMessages();
                }
            }
        } catch (error) {
            // Silently fail
        }
    }

    async markAsRead() {
        try {
            await fetch(`/chat/conversations/${this.conversationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
        } catch (error) {
            // Silently fail
        }
    }

    minimize() {
        this.isMinimized = !this.isMinimized;

        if (this.isMinimized) {
            this.element.classList.add('minimized');
            this.element.style.height = '45px';
        } else {
            this.element.classList.remove('minimized');
            this.element.style.height = '500px';
            this.scrollToBottom();
        }
    }

    makeDraggable() {
        const header = this.element.querySelector('.chat-window-header');
        let isDragging = false;
        let currentX;
        let currentY;
        let initialX;
        let initialY;

        header.addEventListener('mousedown', (e) => {
            if (e.target.tagName === 'BUTTON' || e.target.closest('button')) return;

            isDragging = true;
            initialX = e.clientX - this.element.offsetLeft;
            initialY = e.clientY - this.element.offsetTop;

            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
        });

        const drag = (e) => {
            if (!isDragging) return;

            e.preventDefault();
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;

            // Keep within viewport
            const maxX = window.innerWidth - this.element.offsetWidth;
            const maxY = window.innerHeight - this.element.offsetHeight;

            currentX = Math.max(0, Math.min(currentX, maxX));
            currentY = Math.max(0, Math.min(currentY, maxY));

            this.element.style.right = 'auto';
            this.element.style.bottom = 'auto';
            this.element.style.left = currentX + 'px';
            this.element.style.top = currentY + 'px';
        };

        const stopDrag = () => {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
        };
    }

    makeResizable() {
        const handle = this.element.querySelector('.resize-handle');
        let isResizing = false;
        let startY;
        let startHeight;

        handle.addEventListener('mousedown', (e) => {
            isResizing = true;
            startY = e.clientY;
            startHeight = this.element.offsetHeight;

            document.addEventListener('mousemove', resize);
            document.addEventListener('mouseup', stopResize);
        });

        const resize = (e) => {
            if (!isResizing || this.isMinimized) return;

            const delta = startY - e.clientY;
            const newHeight = Math.max(300, Math.min(800, startHeight + delta));

            this.element.style.height = newHeight + 'px';
        };

        const stopResize = () => {
            isResizing = false;
            document.removeEventListener('mousemove', resize);
            document.removeEventListener('mouseup', stopResize);
        };
    }

    scrollToBottom() {
        const container = document.getElementById(`messages-${this.conversationId}`);
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    showError(message) {
        const container = document.getElementById(`messages-${this.conversationId}`);
        container.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="text-center text-red-500">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>${message}</p>
                </div>
            </div>
        `;
    }

    formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m`;
        if (diffInSeconds < 86400) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    destroy() {
        clearTimeout(this.typingTimeout);
        this.element.remove();
    }
}

// Export ChatWindow to global scope
window.ChatWindow = ChatWindow;

// Export for use in floating-chat-system.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatWindow;
}
