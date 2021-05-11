<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ItemController;
use App\Http\Resources\ItemResource;
use App\Http\Resources\RecipeResource;
use App\Models\Item;
use App\Models\Recipe;

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
    // -- ITEMS
    // specific item
    Route::get('/items/{id}', function ($id) {
        return (new ItemResource(Item::with('recipe')
            ->with('sharedData')
            ->findOrFail($id)));
    })->where('id', '[0-9]+')->name('items.show');
    // by slug
    Route::get('/items/{slug}', function ($slug) {
        return (new ItemResource(Item::where('slug', $slug)
            ->with('recipe')
            ->with('sharedData')
            ->firstOrFail()));
    })->name('items.show');
    // all items
    Route::get('/items', function () {
        return ItemResource::collection(Item::with('recipe')
            ->with('sharedData')
            ->get());
    })->name('items.index');

    // -- RECIPES
    // specific recipe
    Route::get('/recipes/{id}', function ($id) {
        return (new RecipeResource(Recipe::with('requirements')
            ->with('craftingStation')
            ->with('item')
            ->with('item.sharedData')
            ->findOrFail($id)));
    })->where('id', '[0-9]+')->name('recipes.show');
    // by slug
    Route::get('/recipes/{slug}', function ($slug) {
        return (new RecipeResource(Recipe::with('requirements')
            ->with('craftingStation')
            ->with('item')
            ->with('item.sharedData')
            ->where('slug', $slug)
            ->firstOrFail()));
    })->name('recipes.show');
    // all recipes
    Route::get('/recipes', function () {
        return RecipeResource::collection(Recipe::with('requirements')
            ->with('craftingStation')
            ->with('item')
            ->with('item.sharedData')
            ->orderBy('name', 'asc')
            ->get());
    })->name('recipes.index');
});
