<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ItemController;
use App\Models\Item;
use App\Models\Recipe;

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
if (env('APP_ENV') === 'local') {
    Route::prefix('convert')->name('convert.')->group(function () {
        Route::get(
            '/',
            [App\Http\Controllers\ConvertController::class, 'index']
        )->name('index');

        Route::get(
            '/piece-tables/{name?}',
            [App\Http\Controllers\ConvertController::class, 'pieceTable']
        )->name('piece-tables');

        Route::get(
            '/pieces/{name?}',
            [App\Http\Controllers\ConvertController::class, 'piece']
        )->name('pieces');

        Route::get(
            '/pieces/chunk/{offset}',
            [App\Http\Controllers\ConvertController::class, 'pieces']
        )
        ->where('offset', '[0-9]+')
        ->name('pieces.chunk');

        Route::get(
            '/crafting-station/{name?}',
            [App\Http\Controllers\ConvertController::class, 'craftingStation']
        )->name('crafting-station');

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
}

// END Convert ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('index');
Route::get('/home', [App\Http\Controllers\PageController::class, 'index'])->name('index');
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');

/*Route::resources(
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
);*/

// RECIPES
Route::get('/recipes', [App\Http\Controllers\RecipeController::class, 'index'])
    ->name('recipes.index');
Route::get('/recipes/{id}', [App\Http\Controllers\RecipeController::class, 'show'])
    ->name('recipes.show')
    ->where('id', '[0-9]+');
Route::get('/recipes/{slug}', [App\Http\Controllers\RecipeController::class, 'showSlug'])
    ->name('recipes.showSlug');

// ITEMS
Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])
    ->name('items.index');
Route::get('/items/{id}', [App\Http\Controllers\ItemController::class, 'show'])
    ->name('items.show')
    ->where('id', '[0-9]+');
Route::get('/items/{slug}', [App\Http\Controllers\ItemController::class, 'showSlug'])
    ->name('items.showSlug');

// STATUS EFFECTS
Route::get('/status-effects', [App\Http\Controllers\StatusEffectController::class, 'index'])
    ->name('status-effects.index');
