<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemRecipeController;
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
if (config('app.env') === 'local') {

    Route::prefix('convert')
        ->name('convert.')
        ->group(function () {
        
        /*Route::get(
            '/',
            [App\Http\Controllers\ConvertController::class, 'index']
        )->name('index');*/

        Route::get(
            '/crafting-station',
            [App\Http\Controllers\CraftingStationController::class, 'convert']
        )->name('crafting-station');

        Route::get(
            '/status-effect',
            [App\Http\Controllers\StatusEffectController::class, 'convert']
        )->name('status-effects');

        Route::get(
            '/piece-table',
            [App\Http\Controllers\PieceTableController::class, 'convert']
        )->name('piece-tables');

        Route::get(
            '/items',
            [App\Http\Controllers\ItemController::class, 'convert']
        )->name('items');
        
        // TODO: 
        Route::get(
            '/piece',
            [App\Http\Controllers\PieceController::class, 'convert']
        )->name('pieces');

        /*Route::get(
            '/pieces/chunk/{offset}',
            [App\Http\Controllers\ConvertController::class, 'pieces']
        )
        ->where('offset', '[0-9]+')
        ->name('pieces.chunk');*/
        
        // TODO: 
        Route::get(
            '/recipes',
            [App\Http\Controllers\ItemRecipeController::class, 'convert']
        )->name('recipes');

        // TODO: piece-recipes, or combine with Pieces convert. OR combine with ItemRecipes and use a RecipesController?
        Route::get(
            '/piece-recipe',
            [App\Http\Controllers\PieceRecipeController::class, 'convert']
        )->name('piece-recipes');

        /*Route::get(
            '/{name}',
            [App\Http\Controllers\ConvertController::class, 'convert']
        )->name('names');*/
    });
}

// END Convert ------------------------------------------------------------------

Route::get('/', [App\Http\Controllers\PageController::class, 'index'])->name('index');
Route::get('/home', [App\Http\Controllers\PageController::class, 'index'])->name('index');
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');

/*Route::resources(
    [
        'status-effects' => StatusEffectController::class,
        'recipes' => ItemRecipeController::class,
        'items' => ItemController::class,
    ],
    // options -- ['index', 'show', 'store', 'update', 'destroy'];
    [
        // don't create routes for modifying the resources
        'only' => ['index', 'show']
    ]
);*/

// RECIPES
Route::get('/recipes', [App\Http\Controllers\ItemRecipeController::class, 'index'])
    ->name('recipes.index');
Route::get('/recipes/{id}', [App\Http\Controllers\ItemRecipeController::class, 'show'])
    ->name('recipes.show')
    ->where('id', '[0-9]+');
Route::get('/recipes/{slug}', [App\Http\Controllers\ItemRecipeController::class, 'showSlug'])
    ->name('recipes.showSlug');

// ITEMS
Route::get('/items', [App\Http\Controllers\ItemController::class, 'index'])
    ->name('items.index');
Route::get('/items/{item:slug}', [App\Http\Controllers\ItemController::class, 'show'])
    ->name('items.show')
    ->where('slug', '[a-zA-Z0-9-]+');
/*Route::get('/items/{slug}', [App\Http\Controllers\ItemController::class, 'showSlug'])
    ->name('items.showSlug');*/

// PIECES
Route::get('/pieces', [App\Http\Controllers\PieceController::class, 'index'])
    ->name('pieces.index');
Route::get('/pieces/{id}', [App\Http\Controllers\PieceController::class, 'show'])
    ->name('pieces.show')
    ->where('id', '[0-9]+');
Route::get('/pieces/{slug}', [App\Http\Controllers\PieceController::class, 'showSlug'])
    ->name('pieces.showSlug');

// STATUS EFFECTS
Route::get('/status-effects', [App\Http\Controllers\StatusEffectController::class, 'index'])
    ->name('status-effects.index');
