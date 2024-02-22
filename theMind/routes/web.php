<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games/join', [GameController::class, 'join'])->name('games.join');
    Route::get('/game/{id}', [GameController::class, 'show'])->name('game.show');
    Route::post('/game/{game}/start', [GameController::class, 'start'])->name('game.start');
    Route::post('/game/{game}/play', [GameController::class, 'playCard'])->name('game.play');


});

require __DIR__.'/auth.php';
