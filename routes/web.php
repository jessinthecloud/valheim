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

// Convert ------------------------------------------------------------------

Route::get(
    '/convert/',
    [App\Http\Controllers\ConvertController::class, 'index']
)->name('convert.index');

Route::get(
    '/convert/recipe/{name?}',
    [App\Http\Controllers\ConvertController::class, 'recipe']
)->name('convert.recipe');

Route::get(
    '/convert/item/{name?}',
    [App\Http\Controllers\ConvertController::class, 'item']
)->name('convert.item');

Route::get(
    '/convert/{name}',
    [App\Http\Controllers\ConvertController::class, 'convert']
)->name('convert.name');

// END Convert ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('index');

Route::get('/recipe', [App\Http\Controllers\RecipeController::class, 'index'])->name('recipe.index');

Route::get('/item', [App\Http\Controllers\ItemController::class, 'index'])->name('item.index');
