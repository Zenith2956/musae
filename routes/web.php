<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\SheetController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/calendar/events', [CalendarController::class, 'index']);
Route::put('/calendar/events/{id}', [CalendarController::class, 'update']);
Route::post('/calendar/events', [CalendarController::class, 'store']);
Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy']);
Route::get('/calendar/sheets', [CalendarController::class, 'listSheets']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/post', [SheetController::class, 'store']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::inertia('calendar', 'Calendar')->name('calendar');

    Route::get('/calendar', [CalendarController::class, 'index']);
    Route::get('/calendar/events', [CalendarController::class, 'events']);
    Route::get('/calendar/instruments', [CalendarController::class, 'listInstruments']);

    Route::post('/calendar/events', [CalendarController::class, 'store']);

    Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy']);

    Route::put('/calendar/events/{id}', [CalendarController::class, 'update']);


    Route::get('library/instruments', [SheetController::class, 'listInstruments']);
    Route::get('/library', [SheetController::class, 'index']);


    Route::get('/sheet/{sheet}', [SheetController::class, 'detail'])->name('sheet.detail');
    Route::post('/sheet/store', [SheetController::class, 'store']);


    Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique');
});



require __DIR__ . '/settings.php';
