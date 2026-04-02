<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Settings\CalendarController;

use App\Http\Controllers\SheetController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/calendar/events', [CalendarController::class, 'index']);
Route::put('/calendar/events/{id}', [CalendarController::class, 'update']);
Route::post('/calendar/events', [CalendarController::class, 'store']);
Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('calendar', 'Calendar')->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'events']);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/calendar/events', [CalendarController::class, 'events']);
    Route::post('/calendar/events', [CalendarController::class, 'store']);
    Route::put('/calendar/events/{id}', [CalendarController::class, 'update']);
    Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy']);
    Route::get('/library', [SheetController::class, 'index']);
    Route::post('/post', [SheetController::class, 'store']);
    Route::get('/sheet/{sheet}', [SheetController::class, 'detail'])->name('sheet.detail');
});


require __DIR__.'/settings.php';
