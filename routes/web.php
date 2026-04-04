<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MenteeDashboardController;
use App\Http\Controllers\MentorDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebMentorController;
use App\Http\Controllers\WebSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/mentors', [WebMentorController::class, 'index'])->name('web.mentors.index');
Route::get('/mentors/{mentor}', [WebMentorController::class, 'show'])->name('web.mentors.show');

Route::get('/become-a-mentor', function () {
    return redirect()->route('register', ['role' => 'mentor']);
})->name('become.mentor');

Route::middleware('auth')->group(function () {
    Route::get('/sessions', [WebSessionController::class, 'index'])->name('sessions.index');
    Route::get('/messages', [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [ConversationController::class, 'show'])->name('messages.show');
    Route::get('/messages/{conversation}/feed', [ConversationController::class, 'feed'])->name('messages.feed');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/start/mentor/{mentor}', [ConversationController::class, 'startWithMentor'])->name('messages.start.mentor');
    Route::post('/messages/start/mentee/{mentee}', [ConversationController::class, 'startWithMentee'])->name('messages.start.mentee');
    Route::get('/messages/{conversation}/call/{mode?}', [ConversationController::class, 'call'])->name('messages.call');

    Route::get('/mentor/dashboard', [MentorDashboardController::class, 'index'])
        ->name('mentor.dashboard');

    Route::get('/mentee/dashboard', [MenteeDashboardController::class, 'index'])
        ->name('mentee.dashboard');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'mentor' => redirect()->route('mentor.dashboard'),
            'mentee' => redirect()->route('mentee.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
