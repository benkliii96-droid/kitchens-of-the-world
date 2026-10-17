<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\StepController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/webhook', WebhookController::class);

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::get('/recepts', [RecipeController::class, 'index']);
    Route::get('/recepts/{id}', [RecipeController::class, 'show'])->whereNumber('id');

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{recipeId}', [FavoriteController::class, 'destroy'])->whereNumber('recipeId');

    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->whereNumber('id');
    Route::get('/purchases/{id}/progress', [PurchaseController::class, 'progress'])->whereNumber('id');

    Route::post('/steps/{stepId}/complete', [StepController::class, 'complete'])->whereNumber('stepId');
});
