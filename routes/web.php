<?php

use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // GROUP routes
    Route::name('group')->prefix('/group')->controller(GroupController::class)->group(function(){
        Route::post('/', 'store');
        Route::patch('/{group}', 'update');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
