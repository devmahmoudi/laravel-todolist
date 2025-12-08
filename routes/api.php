<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TodoController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    Route::name('group.')->prefix('/group')->controller(GroupController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{group}', 'show')->name('show');
        Route::put('/{group}', 'update')->name('update');
        Route::delete('/{group}', 'destroy')->name('destroy');
    });

    Route::post('/todo', [TodoController::class, 'store'])->name('todo.store');
    Route::get('/{group}/todo', [TodoController::class, 'index'])->name('group.todo');
    Route::get('/todo/{todo}', [TodoController::class, 'show'])->name('todo.show');
    Route::put('/todo/{todo}', [TodoController::class, 'update'])->name('todo.update');
    Route::patch('/todo/{todo}/toggle-completed', [TodoController::class, 'toggleCompleted'])->name('todo.toggle-completed');
    Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])->name('todo.delete');
});
