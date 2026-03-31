<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Settings\CalendarController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/calendar/events', [CalendarController::class, 'index']);
Route::put('/calendar/events/{id}', [CalendarController::class, 'update']);
Route::post('/calendar/events', [CalendarController::class, 'store']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('calendar', 'Calendar')->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'events']);
});



require __DIR__ . '/settings.php';
