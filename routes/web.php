<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Valheim ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('valheim.index');

Route::get('/recipes', [App\Http\Controllers\RecipeController::class, 'index'])->name('valheim.recipes.index');
