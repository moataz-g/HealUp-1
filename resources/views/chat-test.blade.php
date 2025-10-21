<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f3f4f6;
        }
        .test-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .info { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="test-box">
        <h1 style="color: #16a34a; margin-bottom: 20px;">🔍 Chat System Diagnostics</h1>
        
        <div id="auth-status" class="status info">
            Checking authentication...
        </div>
        
        <div id="laravel-status" class="status info">
            Checking Laravel object...
        </div>
        
        <div id="chat-status" class="status info">
            Checking chat system...
        </div>
        
        <div id="elements-status" class="status info">
            Checking DOM elements...
        </div>
        
        <div id="console-logs" class="test-box" style="background: #1f2937; color: #10b981; font-family: monospace; padding: 15px; overflow-x: auto;">
            <h3 style="color: #10b981; margin-bottom: 10px;">Console Output:</h3>
            <div id="log-output"></div>
        </div>
    </div>

    <!-- User Data Script -->
    @auth
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: {
                id: {{ auth()->id() }},
                name: '{{ addslashes(auth()->user()->name) }}',
                email: '{{ auth()->user()->email }}',
                avatar: '{{ auth()->user()->profile_photo_url }}',
                role: '{{ auth()->user()->role }}'
            }
        };
        console.log('✅ User data loaded:', window.Laravel.user);
    </script>
    @else
    <script>
        console.log('❌ User is not authenticated');
    </script>
    @endauth

    <script>
        // Intercept console logs
        const logOutput = document.getElementById('log-output');
        const originalLog = console.log;
        const originalError = console.error;
        
        console.log = function(...args) {
            logOutput.innerHTML += '<div style="color: #10b981;">▶ ' + args.join(' ') + '</div>';
            originalLog.apply(console, args);
        };
        
        console.error = function(...args) {
            logOutput.innerHTML += '<div style="color: #ef4444;">✖ ' + args.join(' ') + '</div>';
            originalError.apply(console, args);
        };

        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🚀 DOM Content Loaded');
            
            // Check authentication
            const authStatus = document.getElementById('auth-status');
            @auth
                authStatus.className = 'status success';
                authStatus.textContent = '✅ User authenticated: {{ auth()->user()->name }}';
            @else
                authStatus.className = 'status error';
                authStatus.textContent = '❌ User not authenticated - Please login first';
            @endauth
            
            // Check Laravel object
            const laravelStatus = document.getElementById('laravel-status');
            if (window.Laravel && window.Laravel.user) {
                laravelStatus.className = 'status success';
                laravelStatus.textContent = '✅ Laravel object found with user data';
                console.log('Laravel user:', window.Laravel.user);
            } else {
                laravelStatus.className = 'status error';
                laravelStatus.textContent = '❌ Laravel object or user data not found';
            }
            
            // Check chat system
            setTimeout(() => {
                const chatStatus = document.getElementById('chat-status');
                if (window.floatingChat) {
                    chatStatus.className = 'status success';
                    chatStatus.textContent = '✅ Chat system initialized successfully';
                    console.log('Chat system:', window.floatingChat);
                } else {
                    chatStatus.className = 'status error';
                    chatStatus.textContent = '❌ Chat system not initialized';
                }
                
                // Check DOM elements
                const elementsStatus = document.getElementById('elements-status');
                const button = document.getElementById('floating-chat-button');
                const sidebar = document.getElementById('floating-chat-sidebar');
                
                if (button && sidebar) {
                    elementsStatus.className = 'status success';
                    elementsStatus.textContent = '✅ Chat button and sidebar elements found in DOM';
                    console.log('Button:', button);
                    console.log('Sidebar:', sidebar);
                } else {
                    elementsStatus.className = 'status error';
                    elementsStatus.textContent = '❌ Chat elements not found - Button: ' + (button ? 'Found' : 'Missing') + ', Sidebar: ' + (sidebar ? 'Found' : 'Missing');
                }
            }, 1000);
        });
    </script>
</body>
</html>
