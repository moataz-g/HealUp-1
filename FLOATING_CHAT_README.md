# 💬 Floating Real-Time Chat System

A modern, Facebook/LinkedIn-style floating chat interface for the HealUp application.

![Chat System](https://img.shields.io/badge/Version-1.0.0-blue)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4-38B2AC)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-yellow)

---

## 📸 Preview

### Main Features
- 🔵 Floating chat button with unread badge
- 🔍 User search functionality
- 💬 Multiple chat windows
- ⚡ Real-time messaging
- 👁️ Read receipts
- ⌨️ Typing indicators
- 🌙 Dark mode support
- 📱 Fully responsive

---

## ⚡ Quick Start

### 1. Installation
```bash
# Already completed! ✅
php artisan migrate
npm run dev
```

### 2. Usage
1. Login to your HealUp account
2. Look for the blue chat button in the bottom-right corner
3. Click it and start chatting!

---

## 🎯 Key Features

### Real-Time Communication
- **Instant Messaging**: Send and receive messages in real-time
- **Typing Indicators**: See when the other person is typing
- **Read Receipts**: Know when your message has been read
- **Online Status**: See who's currently online

### User Interface
- **Floating Button**: Non-intrusive access from any page
- **Search Bar**: Find users quickly by name or email
- **Chat Windows**: Resizable, draggable, and minimizable
- **Multiple Chats**: Open several conversations simultaneously

### User Experience
- **Auto-scroll**: Automatically scrolls to new messages
- **Timestamps**: Shows when each message was sent
- **Avatars**: User profile pictures in every message
- **Role Badges**: Identify students, professors, and admins

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────┐
│              Floating Chat System               │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌──────────────┐      ┌──────────────┐       │
│  │ Chat Button  │──────│  Sidebar     │       │
│  │  (Fixed)     │      │  - Search    │       │
│  └──────────────┘      │  - List      │       │
│                        └──────────────┘       │
│                                                 │
│  ┌──────────────┐  ┌──────────────┐           │
│  │ Chat Window  │  │ Chat Window  │  ...      │
│  │  (Draggable) │  │  (Draggable) │           │
│  └──────────────┘  └──────────────┘           │
│                                                 │
└─────────────────────────────────────────────────┘
         │                    │
         ▼                    ▼
┌─────────────────────────────────────────────────┐
│              Backend API (Laravel)              │
├─────────────────────────────────────────────────┤
│  • ChatController                               │
│  • Conversation Model                           │
│  • ConversationMessage Model                    │
│  • TypingIndicator Model                        │
└─────────────────────────────────────────────────┘
         │                    │
         ▼                    ▼
┌─────────────────────────────────────────────────┐
│              Database (MySQL)                   │
├─────────────────────────────────────────────────┤
│  • conversations                                │
│  • conversation_messages                        │
│  • typing_indicators                            │
└─────────────────────────────────────────────────┘
```

---

## 📁 File Structure

```
HealUp/
├── app/
│   ├── Http/Controllers/
│   │   └── ChatController.php                 # API endpoints
│   └── Models/
│       ├── Conversation.php                   # Conversation model
│       ├── ConversationMessage.php            # Message model
│       └── TypingIndicator.php                # Typing status
│
├── database/migrations/
│   └── 2025_01_15_000001_create_conversations_table.php
│
├── resources/
│   ├── css/
│   │   └── floating-chat.css                  # Chat styles
│   └── js/
│       ├── floating-chat-system.js            # Main chat system
│       └── chat-window.js                     # Chat window component
│
└── documentation/
    ├── FLOATING_CHAT_SYSTEM_DOCUMENTATION.md  # Full docs
    └── FLOATING_CHAT_SETUP.md                 # Setup guide
```

---

## 🔌 API Endpoints

### Conversations
```http
GET    /chat/conversations                     # List conversations
POST   /chat/conversations/start               # Start new conversation
DELETE /chat/conversations/{id}                # Delete conversation
```

### Messages
```http
GET    /chat/conversations/{id}/messages       # Get messages
POST   /chat/conversations/{id}/messages       # Send message
POST   /chat/conversations/{id}/mark-read      # Mark as read
```

### Real-Time Features
```http
POST   /chat/conversations/{id}/typing         # Update typing status
GET    /chat/conversations/{id}/typing         # Check typing status
GET    /chat/unread-count                      # Get unread count
```

### Users
```http
GET    /chat/users/search?query={term}         # Search users
POST   /chat/online                            # Set online status
```

---

## 🎨 Customization

### Change Colors

Edit `resources/css/floating-chat.css`:

```css
/* Primary Button Color */
#floating-chat-button {
    background: #3B82F6; /* Change this */
}

/* Header Gradient */
.chat-window-header {
    background: linear-gradient(to right, #3B82F6, #2563EB);
}
```

### Change Polling Interval

Edit `resources/js/floating-chat-system.js`:

```javascript
startPolling() {
    this.pollInterval = setInterval(() => {
        // ... polling logic
    }, 5000); // 5 seconds - change as needed
}
```

### Change Window Dimensions

Edit `resources/js/chat-window.js`:

```javascript
createElement() {
    // Window width
    window.className = '... w-[350px] ...'; // Change width
    
    // Window height
    window.style.height = '500px'; // Change default height
}
```

---

## 💡 Usage Examples

### Starting a Conversation (JavaScript)

```javascript
// Start conversation with user ID 5
window.floatingChat.startConversation(5, {
    id: 5,
    name: 'John Doe',
    email: 'john@example.com',
    avatar: 'https://...',
    role: 'student',
    is_online: true
});
```

### Sending a Message (API)

```javascript
const response = await fetch('/chat/conversations/1/messages', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').content,
    },
    body: JSON.stringify({
        message: 'Hello!'
    })
});
```

### Backend - Get Conversations (PHP)

```php
use App\Http\Controllers\ChatController;

$controller = new ChatController();
$conversations = $controller->getConversations($request);
```

---

## 🔐 Security Features

- ✅ CSRF Protection on all POST requests
- ✅ User authorization (users can only access their own conversations)
- ✅ XSS Prevention (HTML escaping)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ Input validation and sanitization
- ✅ Rate limiting ready (can be enabled in routes)

---

## 📊 Performance

### Current Implementation
- **Polling Interval**: 5 seconds
- **Message Batch Size**: All messages per conversation
- **Suitable For**: Small to medium applications (< 1000 concurrent users)

### Optimization Recommendations

**For High Traffic:**
1. Implement Laravel WebSockets or Pusher
2. Add message pagination
3. Use Redis for caching
4. Implement database indexes

**Database Indexes:**
```sql
CREATE INDEX idx_conversations_users ON conversations(user_one_id, user_two_id);
CREATE INDEX idx_messages_conv ON conversation_messages(conversation_id, created_at);
CREATE INDEX idx_messages_unread ON conversation_messages(is_read, sender_id);
```

---

## 🧪 Testing

### Manual Testing Checklist

- [ ] Chat button appears for logged-in users
- [ ] Search finds users correctly
- [ ] Conversation starts successfully
- [ ] Messages send and receive
- [ ] Typing indicator works
- [ ] Read receipts appear
- [ ] Unread badge updates
- [ ] Windows are draggable
- [ ] Windows are resizable
- [ ] Windows minimize/restore
- [ ] Multiple windows work
- [ ] Dark mode works
- [ ] Mobile responsive

### Browser Console Tests

```javascript
// Check if chat system is loaded
console.log(window.floatingChat);

// Check user data
console.log(window.Laravel.user);

// Open chat programmatically
window.floatingChat.openSidebar();

// Check open windows
console.log(window.floatingChat.openWindows);
```

---

## 🐛 Common Issues & Solutions

### Issue: Chat button not appearing
**Solution:** 
- Ensure you're logged in
- Check console for errors
- Verify `window.Laravel.user` exists
- Rebuild assets: `npm run build`

### Issue: Messages not real-time
**Solution:**
- Check polling is running
- Verify API endpoints work
- Check browser network tab
- Increase polling frequency if needed

### Issue: Styling broken
**Solution:**
- Clear browser cache
- Rebuild assets: `npm run dev`
- Check Tailwind is configured correctly

---

## 🚀 Future Enhancements

### Planned Features
- [ ] File attachments (images, documents)
- [ ] Group chats
- [ ] Voice messages
- [ ] Video calls
- [ ] Message reactions (emoji)
- [ ] Message editing and deletion
- [ ] Rich text formatting
- [ ] User blocking
- [ ] Conversation archiving
- [ ] Desktop notifications
- [ ] Mobile app integration

### Upgrade to WebSockets

For production with many users:

```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"
php artisan migrate
php artisan websockets:serve
```

---

## 📚 Documentation

### Complete Documentation
See `documentation/FLOATING_CHAT_SYSTEM_DOCUMENTATION.md` for:
- Detailed API reference
- Component architecture
- Advanced customization
- Security best practices
- Performance optimization
- Troubleshooting guide

### Quick Setup
See `FLOATING_CHAT_SETUP.md` for installation instructions.

---

## 🤝 Contributing

To improve the chat system:
1. Follow the existing code structure
2. Add comments for complex logic
3. Test thoroughly
4. Update documentation

---

## 📄 License

Part of the HealUp application.

---

## 👥 Credits

Built with modern web technologies:
- **Laravel 11** - Backend framework
- **TailwindCSS** - Styling
- **Vanilla JavaScript** - Frontend logic
- **Alpine.js** - UI interactions

---

## 📞 Support

For issues:
1. Check the documentation
2. Review browser console
3. Check Laravel logs
4. Verify migrations ran
5. Ensure assets are compiled

---

**Made with ❤️ for HealUp**

*Bringing people together through seamless communication*
