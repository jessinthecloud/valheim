<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ItemController;
use App\Http\Resources\ItemResource;
use App\Models\Item;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*
    Automatically prefixed with /api/ and the
    API throttling middleware will be automatically applied
 */
// VERSION 1 ---------------------------------------------------
Route::prefix('v1')->name('api.v1.')->group(function () {

    /*Route::apiResource('recipes', RecipeController::class)->only([
        'index', 'show'
    ]);*/
    /*Route::apiResources(
        [
            'recipes' => RecipeController::class,
            'items' => ItemController::class,
        ],
        // options -- ['index', 'show', 'store', 'update', 'destroy'];
        [
            // don't create routes for modifying the resources
            'only' => ['index', 'show']
        ]
    );*/



    // https://laravel.com/docs/8.x/eloquent-resources
    // specific item
    Route::get('/items/{slug}', function ($slug) {
        return new ItemResource(Item::where('slug', $slug)->firstOrFail());
    })->name('items.show');
    // all items
    Route::get('/items', function () {
        return ItemResource::collection(Item::all());
    })->name('items.index');



    /*Route::get(
        '/recipes',
        [RecipeController::class, 'index']
    )->name('recipe');

    Route::get(
        '/recipes/{slug}',
        [RecipeController::class, 'show']
    )->name('recipe');

    Route::get(
        '/items',
        [ItemController::class, 'index']
    )->name('item');

    Route::get(
        '/items/{slug}',
        [ItemController::class, 'show']
    )->name('item');*/
});
