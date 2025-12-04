<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard', 301)->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // GROUP routes (resource-style API)
    Route::name('group.')->prefix('/group')->controller(GroupController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{group}', 'show')->name('show');
        Route::patch('/{group}', 'update')->name('update');
        Route::delete('/{group}', 'destroy')->name('destroy');
    });

    Route::post('/todo', [TodoController::class, 'store'])->name('todo.store');
    Route::get('/{group}/todo', [TodoController::class, 'index'])->name('group.todo');
    Route::get('/todo/{todo}', [TodoController::class, 'show'])->name('todo.show');
    Route::put('/todo/{todo}', [TodoController::class, 'update'])->name('todo.update');
    Route::patch('/todo/{todo}/toggle-completed', [TodoController::class, 'toggleCompleted'])->name('todo.toggle-completed');
    Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])->name('todo.delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
