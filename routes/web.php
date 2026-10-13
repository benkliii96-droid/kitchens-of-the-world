<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\StepController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', function () {
            return redirect()->route('admin.recipes.index');
        })->name('home');

        Route::get('/recipes', function () {
            return view('admin.recipes.index');
        })->name('recipes.index');

        Route::resource('categories', CategoryController::class);
        Route::resource('ingredients', IngredientController::class);
        Route::resource('steps', StepController::class);
    });
});


