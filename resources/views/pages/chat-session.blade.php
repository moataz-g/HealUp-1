<x-app-layout>
    <div class="max-w-4xl mx-auto px-6 py-12">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Chat Session - {{ $session->advice->title }}
        </h1>

        <!-- Chat Messages -->
        <div id="chat-box"
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    rounded-2xl shadow p-6 h-[500px] overflow-y-auto space-y-4">
            @forelse($session->messages as $message)
                <div class="flex {{ $message->sender === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs px-4 py-2 rounded-xl
                        {{ $message->sender === 'user'
                            ? 'bg-green-600 text-white'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                        {{ $message->content }}
                        <div class="text-xs opacity-70 mt-1">
                            {{ $message->sent_at?->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center">No messages yet. Start the conversation 👋</p>
            @endforelse
        </div>

        <!-- Auto-scroll -->
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const box = document.getElementById('chat-box');
            if (box) box.scrollTop = box.scrollHeight;
        });
        </script>
        @endpush



        <!-- Message Form -->
        <form action="{{ route('chat.messages.store', $session->id) }}" method="POST" class="mt-4 flex space-x-2">
            @csrf
            <input type="text" name="content" placeholder="Type your message..."
                   class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:ring-green-500 focus:border-green-500">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl">
                Send
            </button>
        </form>
    </div>
</x-app-layout>
