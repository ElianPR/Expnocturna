<?php

use App\Http\Controllers\EventShareController;
use App\Http\Controllers\EventController;
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

Route::get('/event/{id_evento}', function (string $id_evento) 
{
    if (! ctype_xdigit($id_evento) || strlen($id_evento) !== 32) {
        abort(404);
    }

    $event = Event::where('id', hex2bin($id_evento))->firstOrFail();

    return view('events.show', compact('event'));
})->name('events.show');

Route::get('/event/{id_evento}/compartir', [EventShareController::class, 'create'])
    ->name('events.share.create');

Route::post('/event/{id_evento}/compartir', [EventShareController::class, 'store'])
    ->name('events.share.store');

require __DIR__.'/auth.php';
