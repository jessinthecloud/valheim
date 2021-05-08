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
Route::prefix('convert')->name('convert.')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\ConvertController::class, 'index']
    )->name('index');

    Route::get(
        '/status-effect/{name?}',
        [App\Http\Controllers\ConvertController::class, 'statusEffect']
    )->name('status-effect');

    Route::get(
        '/recipe/{name?}',
        [App\Http\Controllers\ConvertController::class, 'recipe']
    )->name('recipe');

    Route::get(
        '/item/{name?}',
        [App\Http\Controllers\ConvertController::class, 'item']
    )->name('item');

    Route::get(
        '/{name}',
        [App\Http\Controllers\ConvertController::class, 'convert']
    )->name('name');
});

// END Convert ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('index');

Route::get('/recipe', [App\Http\Controllers\RecipeController::class, 'index'])->name('recipe.index');
Route::get('/recipe/{name}', [App\Http\Controllers\RecipeController::class, 'show'])->name('recipe.show');

Route::get('/item', [App\Http\Controllers\ItemController::class, 'index'])->name('item.index');
Route::get('/item/{slug}', [App\Http\Controllers\ItemController::class, 'index'])->name('item.index');
