<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-8">
            Personalized Advice & Chat
        </h1>

        <!-- Advice List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($advices as $advice)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">
                        {{ $advice->title }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                        {{ $advice->content }}
                    </p>
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>By: {{ $advice->generated_by }}</span>
                        <span class="italic">{{ $advice->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <!-- Start Chat Button -->
                        <a href="{{ route('chat.sessions.start', $advice->id) }}"
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center transition">
                            Start Chat
                        </a>
                        <!-- View Details -->
                        <a href="{{ route('advices.show', $advice->id) }}"
                           class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-center transition">
                            View
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Active Chat Sessions -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-12 mb-6">Active Chat Sessions</h2>
        <div class="space-y-4">
            @forelse($chatSessions as $session)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                                Advice: {{ $session->advice->title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Started {{ $session->started_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('chat.sessions.show', $session->id) }}"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                                Open Chat
                            </a>

                            <!-- Delete form -->
                            <form action="{{ route('chat.sessions.destroy', $session->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this chat session?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No active chat sessions yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
