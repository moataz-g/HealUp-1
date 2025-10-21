<?php

// Professor: View event participants
Route::get('/events/{event}/participants', [App\Http\Controllers\EventController::class, 'participants'])->name('events.participants');

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RepasController;
use App\Http\Controllers\AdviceController;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\AdviceController as AdminAdviceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('pages.welcome');
})->name('welcome');

// Our first project route
Route::get('/activities', [ActivityController::class, 'index']);

// Theme routes
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
Route::post('/theme/set', [ThemeController::class, 'setTheme'])->name('theme.set');
Route::get('/theme/current', [ThemeController::class, 'getCurrentTheme'])->name('theme.current');

// Debug route (remove in production)
Route::get('/debug', function () {
    return view('debug');
})->name('debug');

// Chat system test route (remove in production)
Route::get('/chat-test', function () {
    return view('chat-test');
})->name('chat-test')->middleware('auth');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('health.dashboard');
    })->name('dashboard');

    // Health Dashboard - Main health tracking interface
    Route::get('/health', [App\Http\Controllers\HealthDashboardController::class, 'index'])->name('health.dashboard');
    Route::get('/health/api', [App\Http\Controllers\HealthDashboardController::class, 'api'])->name('health.api');
    Route::get('/health/habit-stats', [App\Http\Controllers\HealthDashboardController::class, 'getHabitStats'])->name('health.habit-stats');

    // Habits Management
    Route::resource('habits', App\Http\Controllers\HabitController::class, [
        'parameters' => ['habits' => 'userHabit']
    ]);
    Route::get('/habits-available', [App\Http\Controllers\HabitController::class, 'available'])->name('habits.available');
    Route::post('/habits-add-existing', [App\Http\Controllers\HabitController::class, 'addExisting'])->name('habits.add-existing');

    // Daily Progress
    Route::get('/progress', [App\Http\Controllers\DailyProgressController::class, 'index'])->name('progress.index');
    Route::post('/progress', [App\Http\Controllers\DailyProgressController::class, 'store'])->name('progress.store');
    Route::get('/progress/{userHabit}', [App\Http\Controllers\DailyProgressController::class, 'show'])->name('progress.show');
    Route::put('/progress/{dailyProgress}', [App\Http\Controllers\DailyProgressController::class, 'update'])->name('progress.update');
    Route::delete('/progress/{dailyProgress}', [App\Http\Controllers\DailyProgressController::class, 'destroy'])->name('progress.destroy');
    Route::get('/progress/weekly', [App\Http\Controllers\DailyProgressController::class, 'weekly'])->name('progress.weekly');
    Route::post('/progress/quick-log', [App\Http\Controllers\DailyProgressController::class, 'quickLog'])->name('progress.quick-log');

    // Health Reports
    Route::get('/health/reports', [App\Http\Controllers\HealthReportController::class, 'index'])->name('health.reports.index');
    Route::get('/health/reports/weekly', [App\Http\Controllers\HealthReportController::class, 'weekly'])->name('health.reports.weekly');
    Route::get('/health/reports/monthly', [App\Http\Controllers\HealthReportController::class, 'monthly'])->name('health.reports.monthly');
    Route::get('/health/reports/category-performance', [App\Http\Controllers\HealthReportController::class, 'categoryPerformance'])->name('health.reports.category-performance');
    Route::get('/health/reports/habit-comparison', [App\Http\Controllers\HealthReportController::class, 'habitComparison'])->name('health.reports.habit-comparison');
    Route::get('/health/reports/export-pdf', [App\Http\Controllers\HealthReportController::class, 'exportPdf'])->name('health.reports.export-pdf');




    // Front office wellness events page (main events listing)
    Route::get('/wellness-events', [EventController::class, 'frontoffice'])->name('events.frontoffice');
    Route::get('/events', [EventController::class, 'frontoffice'])->name('events.index'); // Alias for backward compatibility

    // Event registration (student)
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');

    // User event routes (view single event only)
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    // ✅ AJOUTER CES ROUTES NUTRITION
    Route::resource('ingredients', IngredientController::class);
    Route::resource('repas', RepasController::class);

    // Routes API internes pour recherche dynamique
    Route::get('/api/internal/ingredients/search', [IngredientController::class, 'search'])->name('api.ingredients.search');
    Route::get('/api/internal/ingredients/categories', [IngredientController::class, 'getCategories'])->name('api.ingredients.categories');

    // Advice routes
    Route::get('/advices', [AdviceController::class, 'index'])->name('advices.index');
    Route::get('/advices/{id}', [AdviceController::class, 'show'])->name('advices.show');

    // Chat session routes
    Route::get('/chat-sessions/start/{advice}', [ChatSessionController::class, 'start'])->name('chat.sessions.start');
    Route::get('/chat-sessions/{id}', [ChatSessionController::class, 'show'])->name('chat.sessions.show');
    Route::delete('/chat-sessions/{id}', [ChatSessionController::class, 'destroy'])->name('chat.sessions.destroy');

    // Chat messages
    Route::post('/chat-sessions/{id}/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');

    // ===== FLOATING CHAT SYSTEM ROUTES =====
    Route::prefix('chat')->name('chat.')->group(function () {
        // Conversations
        Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'getConversations'])->name('conversations');
        Route::post('/conversations/start', [App\Http\Controllers\ChatController::class, 'startConversation'])->name('conversations.start');
        Route::delete('/conversations/{conversationId}', [App\Http\Controllers\ChatController::class, 'deleteConversation'])->name('conversations.delete');
        
        // Messages
        Route::get('/conversations/{conversationId}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
        Route::post('/conversations/{conversationId}/messages', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('messages.send');
        Route::post('/conversations/{conversationId}/mark-read', [App\Http\Controllers\ChatController::class, 'markAsRead'])->name('messages.mark-read');
        
        // Typing indicators
        Route::post('/conversations/{conversationId}/typing', [App\Http\Controllers\ChatController::class, 'updateTyping'])->name('typing.update');
        Route::get('/conversations/{conversationId}/typing', [App\Http\Controllers\ChatController::class, 'getTypingStatus'])->name('typing.status');
        
        // User search
        Route::get('/users/search', [App\Http\Controllers\ChatController::class, 'searchUsers'])->name('users.search');
        
        // Online status
        Route::post('/online', [App\Http\Controllers\ChatController::class, 'setOnline'])->name('online');
        
        // Unread count
        Route::get('/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('unread-count');
    });
});

// Admin Routes - Protected by auth and admin middleware
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // View participants for an event (admin)
    Route::get('events/{event}/participants', [App\Http\Controllers\Admin\EventController::class, 'participants'])->name('events.participants');
    // Admin Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('profile');

    // Admin API Routes
    Route::get('/api/stats', [App\Http\Controllers\Admin\AdminController::class, 'getStats'])->name('api.stats');
    Route::get('/api/chart-data', [App\Http\Controllers\Admin\AdminController::class, 'getChartData'])->name('api.chart-data');
    Route::get('/api/system-info', [App\Http\Controllers\Admin\AdminController::class, 'systemInfo'])->name('api.system-info');

    // System Management
    Route::post('/cache/clear', [App\Http\Controllers\Admin\AdminController::class, 'clearCache'])->name('cache.clear');
    Route::post('/migrations/run', [App\Http\Controllers\Admin\AdminController::class, 'runMigrations'])->name('migrations.run');
    Route::post('/optimize', [App\Http\Controllers\Admin\AdminController::class, 'optimize'])->name('optimize');

    // User Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Habits Management
    Route::resource('habits', App\Http\Controllers\Admin\HabitController::class);
    Route::get('/habits/{habit}/users', [App\Http\Controllers\Admin\HabitController::class, 'users'])->name('habits.users');

    // Challenges Management
    Route::resource('challenges', App\Http\Controllers\Admin\ChallengeController::class);
    Route::post('/challenges/{challenge}/toggle-status', [App\Http\Controllers\Admin\ChallengeController::class, 'toggleStatus'])->name('challenges.toggle-status');
    Route::get('/challenges/{challenge}/participants', [App\Http\Controllers\Admin\ChallengeController::class, 'participants'])->name('challenges.participants');

    // Reports Management
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');
    Route::get('/reports/habits', [App\Http\Controllers\Admin\ReportController::class, 'habits'])->name('reports.habits');
    Route::get('/reports/challenges', [App\Http\Controllers\Admin\ReportController::class, 'challenges'])->name('reports.challenges');
    Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    // Admin-only Categories Management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Teams Management (TODO: Create TeamController)
    // Route::resource('teams', App\Http\Controllers\Admin\TeamController::class);
    // Route::post('/teams/{team}/toggle-status', [App\Http\Controllers\Admin\TeamController::class, 'toggleStatus'])->name('teams.toggle-status');
    // Route::get('/teams/{team}/members', [App\Http\Controllers\Admin\TeamController::class, 'members'])->name('teams.members');

    // Nutrition Management (TODO: Create NutritionController)
    // Route::get('/nutrition', [App\Http\Controllers\Admin\NutritionController::class, 'index'])->name('nutrition.index');
    // Route::resource('nutrition/ingredients', App\Http\Controllers\Admin\IngredientController::class, ['as' => 'nutrition']);
    // Route::resource('nutrition/repas', App\Http\Controllers\Admin\RepasController::class, ['as' => 'nutrition']);

    // Reports (TODO: Create ReportController)
    // Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    // Route::get('/reports/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');
    // Route::get('/reports/habits', [App\Http\Controllers\Admin\ReportController::class, 'habits'])->name('reports.habits');
    // Route::get('/reports/challenges', [App\Http\Controllers\Admin\ReportController::class, 'challenges'])->name('reports.challenges');
    // Route::get('/reports/export/{type}', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

    // Settings (TODO: Create SettingController)
    // Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    // Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    // Route::post('/settings/backup', [App\Http\Controllers\Admin\SettingController::class, 'backup'])->name('settings.backup');
    // Route::post('/settings/restore', [App\Http\Controllers\Admin\SettingController::class, 'restore'])->name('settings.restore');

    // Event Management
    Route::resource('events', App\Http\Controllers\Admin\EventController::class);

    // Advice Management
    Route::resource('advices', AdminAdviceController::class);
});

// Debug route (remove in production)
Route::get('/debug-habits', function () {
    $habit = App\Models\Habit::with(['userHabits.user'])->first();

    if (!$habit) {
        return 'No habits found';
    }

    return [
        'habit_id' => $habit->id,
        'habit_name' => $habit->name,
        'userHabits_count' => $habit->userHabits->count(),
        'userHabits_details' => $habit->userHabits->map(function ($uh) {
            return [
                'id' => $uh->id,
                'user_name' => $uh->user->name,
                'is_active' => $uh->is_active,
                'created_at' => $uh->created_at
            ];
        })
    ];
});
