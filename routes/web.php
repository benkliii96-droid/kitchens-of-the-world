<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', function () {
            return 'Страница входа — часть 2';
        })->name('login');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.recipes.index');
        })->name('home');

        Route::get('/recipes', function () {
            return 'Список рецептов — часть 6';
        })->name('recipes.index');
    });
});
