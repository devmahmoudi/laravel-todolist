<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TodoController;
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
    Route::name('group.')->prefix('/group')->controller(GroupController::class)->group(function(){
        Route::post('/', 'store')->name('store');
        Route::patch('/{group}', 'update')->name('update');
        Route::delete('/{group}', 'destroy')->name('destroy');
    });

    Route::get('/{group}/todo', [TodoController::class, 'index'])->name('group.todo');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
