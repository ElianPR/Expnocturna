<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventShareController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('/', 'auth.login')->name('home');
});

Route::get('/dashboard', function () {
    $events = Event::all();

    return view('dashboard', compact('events'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Route::get('/events/crear', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
});

Route::get('/event/{id_evento}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');

Route::get('/event/{id}/qr', [EventController::class, 'qr'])
    ->name('events.qr');

Route::get('/event/{id_evento}/file/{filename}', [EventController::class, 'serveFile'])->name('file.show');
Route::get('/album/{id_album}', [EventShareController::class, 'showAlbum'])->name('album.show');

Route::get('/album/{id_album}/file/{filename}', [EventShareController::class, 'serveFile'])->name('album.file');
Route::get('/event/{id_evento}/compartir', [EventShareController::class, 'create'])
    ->name('events.share.create');

Route::post('/event/{id_evento}/compartir', [EventShareController::class, 'store'])
    ->name('events.share.store');

Route::get('/event/{id_evento}/camara', [EventController::class, 'camera'])
    ->name('events.camera');
require __DIR__.'/auth.php';
