<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\AdviceController;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\ChatMessageController;

Route::get('/', function () {
    return view('pages.welcome');
})->name('welcome');

// Our first project route
Route::get('/activities', [ActivityController::class, 'index']);

// Theme routes
Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
Route::post('/theme/set', [ThemeController::class, 'setTheme'])->name('theme.set');
Route::get('/theme/current', [ThemeController::class, 'getCurrentTheme'])->name('theme.current');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Advice routes
    Route::get('/advices', [AdviceController::class, 'index'])->name('advices.index');
    Route::get('/advices/{id}', [AdviceController::class, 'show'])->name('advices.show');

    // Chat session routes
    Route::get('/chat-sessions/start/{advice}', [ChatSessionController::class, 'start'])->name('chat.sessions.start');
    Route::get('/chat-sessions/{id}', [ChatSessionController::class, 'show'])->name('chat.sessions.show');
    Route::delete('/chat-sessions/{id}', [ChatSessionController::class, 'destroy'])->name('chat.sessions.destroy');


    // Chat messages
    Route::post('/chat-sessions/{id}/messages', [ChatMessageController::class, 'store'])->name('chat.messages.store');
});
