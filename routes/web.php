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
        '/status-effects/{name?}',
        [App\Http\Controllers\ConvertController::class, 'statusEffect']
    )->name('status-effects');

    Route::get(
        '/recipes/{name?}',
        [App\Http\Controllers\ConvertController::class, 'recipe']
    )->name('recipes');

    Route::get(
        '/items/{name?}',
        [App\Http\Controllers\ConvertController::class, 'item']
    )->name('items');

    Route::get(
        '/{name}',
        [App\Http\Controllers\ConvertController::class, 'convert']
    )->name('names');
});

// END Convert ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('index');

Route::resources(
    [
        'status-effects' => StatusEffectController::class,
        'recipes' => RecipeController::class,
        'items' => ItemController::class,
    ],
    // options -- ['index', 'show', 'store', 'update', 'destroy'];
    [
        // don't create routes for modifying the resources
        'only' => ['index', 'show']
    ]
);

/*Route::get('/recipes', [App\Http\Controllers\RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{name}', [App\Http\Controllers\RecipeController::class, 'show'])->name('recipes.show');

Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])->name('items.index');
Route::get('/items/{slug}', [App\Http\Controllers\ItemController::class, 'index'])->name('items.index');*/
