# Floating Real-Time Chat System Documentation

## 📋 Table of Contents
1. [Overview](#overview)
2. [Features](#features)
3. [Architecture](#architecture)
4. [Installation](#installation)
5. [Usage](#usage)
6. [API Reference](#api-reference)
7. [Components](#components)
8. [Customization](#customization)
9. [Troubleshooting](#troubleshooting)

---

## 🌟 Overview

The Floating Chat System is a modern, Facebook/LinkedIn-style real-time messaging interface for the HealUp application. It provides seamless communication between users (students, professors, and admins) with a non-intrusive floating interface.

### Key Highlights
- **Non-intrusive**: Floating button and windows that don't disrupt the main workflow
- **Real-time**: Instant message delivery with typing indicators
- **Multi-window**: Support for multiple simultaneous conversations
- **Responsive**: Fully responsive design for desktop and mobile
- **Dark Mode**: Complete dark mode support
- **Accessible**: WCAG 2.1 AA compliant

---

## ✨ Features

### Core Features
1. **Floating Chat Button**
   - Fixed position in bottom-right corner
   - Unread message badge with count
   - Smooth animations and hover effects

2. **Chat Sidebar**
   - User search functionality
   - Recent conversations list
   - Real-time conversation updates
   - Avatar with online status indicators

3. **Chat Windows**
   - Draggable windows
   - Resizable height (300px - 800px)
   - Minimizable interface
   - Multiple windows support
   - Auto-positioning

4. **Messaging Features**
   - Real-time message sending
   - Message history
   - Read receipts (checkmark indicators)
   - Typing indicators
   - Timestamp display
   - Message formatting

5. **User Experience**
   - Smooth animations
   - Loading states
   - Error handling
   - Auto-scroll to latest message
   - Keyboard shortcuts (Enter to send, Shift+Enter for new line)

---

## 🏗️ Architecture

### Database Schema

```
conversations
├── id (primary key)
├── user_one_id (foreign key → users)
├── user_two_id (foreign key → users)
├── last_message_at (timestamp)
├── created_at
└── updated_at

conversation_messages
├── id (primary key)
├── conversation_id (foreign key → conversations)
├── sender_id (foreign key → users)
├── message (text)
├── is_read (boolean)
├── read_at (timestamp)
├── created_at
└── updated_at

typing_indicators
├── id (primary key)
├── conversation_id (foreign key → conversations)
├── user_id (foreign key → users)
├── is_typing (boolean)
├── last_typed_at (timestamp)
├── created_at
└── updated_at
```

### File Structure

```
HealUp/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatController.php
│   └── Models/
│       ├── Conversation.php
│       ├── ConversationMessage.php
│       ├── TypingIndicator.php
│       └── User.php (updated)
├── database/
│   └── migrations/
│       └── 2025_01_15_000001_create_conversations_table.php
├── resources/
│   ├── css/
│   │   ├── app.css (updated)
│   │   └── floating-chat.css
│   ├── js/
│   │   ├── app.js (updated)
│   │   ├── chat-window.js
│   │   └── floating-chat-system.js
│   └── views/
│       └── layouts/
│           └── base.blade.php (updated)
├── routes/
│   └── web.php (updated)
└── documentation/
    └── FLOATING_CHAT_SYSTEM_DOCUMENTATION.md
```

### Technology Stack

**Backend:**
- Laravel 11
- MySQL/PostgreSQL
- RESTful API

**Frontend:**
- Vanilla JavaScript (ES6+)
- TailwindCSS
- Alpine.js (for UI interactions)

**Communication:**
- HTTP Polling (5-second interval)
- AJAX/Fetch API
- CSRF Protection

---

## 🚀 Installation

### Step 1: Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `conversations`
- `conversation_messages`
- `typing_indicators`

### Step 2: Compile Assets

```bash
npm install
npm run dev
```

Or for production:
```bash
npm run build
```

### Step 3: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 4: Verify Installation

1. Login to your application
2. You should see a blue chat button in the bottom-right corner
3. Click it to open the chat sidebar

---

## 📖 Usage

### Starting a Conversation

1. Click the floating chat button (blue circle in bottom-right)
2. Use the search bar to find a user by name or email
3. Click on a user from search results
4. A new chat window will open

### Sending Messages

1. Type your message in the input field at the bottom
2. Press `Enter` to send or click the send button
3. Use `Shift + Enter` to add a new line without sending

### Managing Windows

**Minimize:** Click the minus icon in the window header
**Close:** Click the X icon in the window header
**Drag:** Click and hold the header to drag the window
**Resize:** Hover over the top edge and drag to resize height

### Multiple Conversations

- Open multiple chat windows simultaneously
- Windows automatically position themselves side-by-side
- Each window operates independently

---

## 🔌 API Reference

### Endpoints

#### Get Conversations
```http
GET /chat/conversations
```
Returns list of conversations for authenticated user.

**Response:**
```json
{
  "conversations": [
    {
      "id": 1,
      "user": {
        "id": 2,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": "https://...",
        "role": "student",
        "is_online": true
      },
      "last_message": {
        "id": 5,
        "message": "Hello!",
        "sender_id": 2,
        "is_read": true,
        "created_at": "2025-01-15T10:30:00.000Z"
      },
      "unread_count": 2,
      "last_message_at": "2025-01-15T10:30:00.000Z"
    }
  ]
}
```

#### Search Users
```http
GET /chat/users/search?query={searchTerm}
```
Search for users to start conversations.

**Parameters:**
- `query` (required): Search term (name or email)

**Response:**
```json
{
  "users": [
    {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "avatar": "https://...",
      "role": "student",
      "is_online": true
    }
  ]
}
```

#### Start Conversation
```http
POST /chat/conversations/start
Content-Type: application/json

{
  "user_id": 2
}
```

**Response:**
```json
{
  "conversation": {
    "id": 1,
    "user": { /* user object */ }
  }
}
```

#### Get Messages
```http
GET /chat/conversations/{conversationId}/messages
```

**Response:**
```json
{
  "messages": [
    {
      "id": 1,
      "message": "Hello!",
      "sender_id": 2,
      "sender": {
        "id": 2,
        "name": "John Doe",
        "avatar": "https://..."
      },
      "is_read": true,
      "read_at": "2025-01-15T10:30:00.000Z",
      "created_at": "2025-01-15T10:30:00.000Z"
    }
  ]
}
```

#### Send Message
```http
POST /chat/conversations/{conversationId}/messages
Content-Type: application/json

{
  "message": "Hello there!"
}
```

**Response:**
```json
{
  "message": {
    "id": 6,
    "message": "Hello there!",
    "sender_id": 1,
    "sender": { /* sender object */ },
    "is_read": false,
    "read_at": null,
    "created_at": "2025-01-15T10:31:00.000Z"
  }
}
```

#### Mark as Read
```http
POST /chat/conversations/{conversationId}/mark-read
```

**Response:**
```json
{
  "success": true
}
```

#### Update Typing Status
```http
POST /chat/conversations/{conversationId}/typing
Content-Type: application/json

{
  "is_typing": true
}
```

**Response:**
```json
{
  "success": true
}
```

#### Get Typing Status
```http
GET /chat/conversations/{conversationId}/typing
```

**Response:**
```json
{
  "is_typing": true
}
```

#### Get Unread Count
```http
GET /chat/unread-count
```

**Response:**
```json
{
  "unread_count": 5
}
```

#### Set Online Status
```http
POST /chat/online
```

**Response:**
```json
{
  "success": true
}
```

#### Delete Conversation
```http
DELETE /chat/conversations/{conversationId}
```

**Response:**
```json
{
  "success": true
}
```

---

## 🧩 Components

### FloatingChatSystem Class

Main class that manages the entire chat system.

**Methods:**
- `init()` - Initialize the chat system
- `createChatButton()` - Create the floating chat button
- `createChatSidebar()` - Create the sidebar interface
- `toggleSidebar()` - Toggle sidebar visibility
- `openSidebar()` - Open the sidebar
- `closeSidebar()` - Close the sidebar
- `loadConversations()` - Load user conversations
- `renderConversations()` - Render conversations list
- `searchUsers(query)` - Search for users
- `startConversation(userId, user)` - Start a new conversation
- `openChatWindow(conversationId, user)` - Open a chat window
- `closeChatWindow(conversationId)` - Close a chat window
- `repositionWindows()` - Reposition all open windows
- `updateUnreadCount()` - Update unread message count
- `setOnlineStatus()` - Set user online status
- `startPolling()` - Start polling for updates
- `stopPolling()` - Stop polling
- `formatTime(timestamp)` - Format time display
- `getRoleBadgeClass(role)` - Get CSS class for role badge
- `destroy()` - Clean up and destroy the system

### ChatWindow Class

Individual chat window component.

**Methods:**
- `createElement()` - Create window DOM element
- `setupEventListeners()` - Setup event listeners
- `loadMessages()` - Load conversation messages
- `renderMessages()` - Render messages in window
- `sendMessage(event)` - Send a new message
- `pollMessages()` - Poll for new messages
- `handleKeyDown(event)` - Handle keyboard events
- `handleTyping()` - Handle typing indicator
- `updateTypingStatus(isTyping)` - Update typing status
- `checkTypingStatus()` - Check if other user is typing
- `markAsRead()` - Mark messages as read
- `minimize()` - Toggle minimize state
- `makeDraggable()` - Make window draggable
- `makeResizable()` - Make window resizable
- `scrollToBottom()` - Scroll to bottom of messages
- `showError(message)` - Display error message
- `formatMessageTime(timestamp)` - Format message time
- `escapeHtml(text)` - Escape HTML in messages
- `destroy()` - Clean up and destroy window

---

## 🎨 Customization

### Changing Colors

Edit `resources/css/floating-chat.css`:

```css
/* Change primary color */
#floating-chat-button {
    background: #your-color;
}

.chat-window-header {
    background: linear-gradient(to right, #your-color1, #your-color2);
}
```

### Changing Polling Interval

Edit `resources/js/floating-chat-system.js`:

```javascript
startPolling() {
    // Change 5000 to your desired interval in milliseconds
    this.pollInterval = setInterval(() => {
        // ...
    }, 5000); // 5 seconds
}
```

### Changing Window Size

Edit `resources/js/chat-window.js`:

```javascript
createElement() {
    // Change default height
    window.style.height = '500px'; // Change this value
    
    // ...
}

makeResizable() {
    // Change min and max height
    const newHeight = Math.max(300, Math.min(800, startHeight + delta));
    //                          ^min      ^max
}
```

### Changing Window Width

Edit `resources/js/floating-chat-system.js`:

```javascript
repositionWindows() {
    const windowWidth = 350; // Change this value
    // ...
}
```

Edit `resources/js/chat-window.js`:

```javascript
createElement() {
    // Change width
    window.className = '... w-[350px] ...'; // Change this value
}
```

### Adding Custom Styling

Add your custom styles to `resources/css/floating-chat.css`:

```css
/* Your custom styles */
.chat-window {
    /* Your modifications */
}
```

---

## 🐛 Troubleshooting

### Chat button not appearing

**Problem:** The floating chat button doesn't show up.

**Solutions:**
1. Verify you're logged in (chat only shows for authenticated users)
2. Check browser console for JavaScript errors
3. Ensure assets are compiled: `npm run dev`
4. Clear cache: `php artisan cache:clear`
5. Check that `window.Laravel.user` exists in browser console

### Messages not sending

**Problem:** Messages fail to send or get stuck.

**Solutions:**
1. Check CSRF token is present in meta tag
2. Verify API routes are working: `/chat/conversations/{id}/messages`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure database connection is working
5. Check browser network tab for failed requests

### Typing indicators not working

**Problem:** Typing indicators don't show up.

**Solutions:**
1. Verify polling is active (check console for errors)
2. Check typing indicator timeout (default 3 seconds)
3. Ensure database table `typing_indicators` exists
4. Check API endpoint: `/chat/conversations/{id}/typing`

### Windows not positioning correctly

**Problem:** Chat windows overlap or position incorrectly.

**Solutions:**
1. Refresh the page
2. Close all windows and reopen
3. Check window width calculation in `repositionWindows()`
4. Verify CSS is loaded correctly

### Dark mode issues

**Problem:** Chat doesn't respect dark mode.

**Solutions:**
1. Verify theme system is working
2. Check `dark:` classes in templates
3. Ensure TailwindCSS dark mode is enabled
4. Reload the page

### Real-time updates not working

**Problem:** New messages don't appear automatically.

**Solutions:**
1. Check polling interval is running (default 5 seconds)
2. Verify browser tab is active (some browsers throttle inactive tabs)
3. Check network connectivity
4. Look for errors in browser console
5. Verify API endpoints are accessible

### Database errors

**Problem:** Errors related to database tables or queries.

**Solutions:**
1. Run migrations: `php artisan migrate`
2. Check database connection in `.env`
3. Verify table structure matches migration
4. Check foreign key constraints
5. Review Laravel logs for SQL errors

---

## 📊 Performance Considerations

### Polling vs WebSockets

The current implementation uses HTTP polling (5-second interval) for simplicity. For higher performance with many users, consider upgrading to:

**Laravel WebSockets or Pusher:**
```bash
composer require beyondcode/laravel-websockets
# or
composer require pusher/pusher-php-server
```

This would provide:
- Instant message delivery
- Reduced server load
- Better scalability
- Real-time presence tracking

### Database Optimization

**Add Indexes:**
```sql
CREATE INDEX idx_conversations_users ON conversations(user_one_id, user_two_id);
CREATE INDEX idx_messages_conversation ON conversation_messages(conversation_id, created_at);
CREATE INDEX idx_messages_read ON conversation_messages(is_read, sender_id);
```

**Query Optimization:**
- Use eager loading to reduce N+1 queries
- Implement pagination for message history
- Cache conversation lists

### Caching Strategy

Implement caching for better performance:

```php
// Cache conversation list
$conversations = Cache::remember("user.{$userId}.conversations", 60, function() {
    return $this->loadConversations();
});

// Cache unread count
$unreadCount = Cache::remember("user.{$userId}.unread", 30, function() {
    return $this->getUnreadCount();
});
```

---

## 🔐 Security Considerations

1. **CSRF Protection:** All POST requests include CSRF token
2. **Authorization:** Conversations are scoped to authenticated users
3. **Input Sanitization:** Messages are escaped before display
4. **SQL Injection:** Using Eloquent ORM prevents SQL injection
5. **XSS Prevention:** HTML entities are escaped in messages

### Additional Security Measures

```php
// Rate limiting
Route::middleware('throttle:60,1')->group(function () {
    // Chat routes
});

// Message validation
$request->validate([
    'message' => 'required|string|max:5000',
]);

// User authorization
if (!$conversation->userOne->is($user) && !$conversation->userTwo->is($user)) {
    abort(403);
}
```

---

## 🎯 Future Enhancements

### Planned Features

1. **File Attachments**
   - Images
   - Documents
   - Voice messages

2. **Group Chats**
   - Multiple participants
   - Admin controls
   - Group naming

3. **Rich Text**
   - Markdown support
   - Emoji picker
   - Link previews

4. **Notifications**
   - Browser notifications
   - Sound alerts
   - Desktop notifications

5. **Advanced Features**
   - Message search
   - Message editing
   - Message deletion
   - Conversation archiving
   - User blocking

6. **Mobile App**
   - React Native
   - Push notifications
   - Offline support

---

## 📞 Support

For issues or questions:
- Check this documentation first
- Review Laravel logs: `storage/logs/laravel.log`
- Check browser console for errors
- Verify database migrations are run
- Ensure assets are compiled

---

## 📝 Changelog

### Version 1.0.0 (2025-01-15)
- Initial release
- Core chat functionality
- Real-time messaging
- Typing indicators
- Read receipts
- Multi-window support
- Drag and resize
- Dark mode support
- Responsive design

---

## 📄 License

This chat system is part of the HealUp application and follows the same license.

---

**Built with ❤️ for HealUp**
