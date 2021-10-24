<?php

use Illuminate\Support\Facades\Route;

// include convert routes
include_once( base_path('routes/convert.php') );

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

// PAGES
Route::get( '/', [App\Http\Controllers\PageController::class, 'index'] )->name( 'index' );
Route::get( '/home', [App\Http\Controllers\PageController::class, 'index'] )->name( 'index' );
Route::get( '/about', [App\Http\Controllers\PageController::class, 'about'] )->name( 'about' );

// get and save images
Route::get( '/images', App\Http\Controllers\Conversion\SaveImageController::class )->name( 'images' );

// ITEMS
Route::group([
    'prefix' => 'items',
    'as' => 'items.'
], function(){
// ALL
    Route::get( '/', [App\Http\Controllers\ItemController::class, 'index'] )
        ->name( 'index' );
    
// ALL
    Route::get( '/{item:slug}', [App\Http\Controllers\ItemController::class, 'show'] )
        ->name( 'show' )
        ->where( 'slug', '[a-zA-Z0-9-]+' );
});

// ARMOR
Route::get( '/armor', [App\Http\Controllers\ArmorController::class, 'index'] )
    ->name( 'armor.index' );
Route::get( '/armor/{armor:slug}', [App\Http\Controllers\ArmorController::class, 'show'] )
    ->name( 'armor.show' )
    ->where( 'slug', '[a-zA-Z0-9-]+' );

// WEAPONS
Route::get( '/weapons', [App\Http\Controllers\WeaponController::class, 'index'] )
    ->name( 'weapons.index' );
Route::get( '/weapon/{weapon:slug}', [App\Http\Controllers\WeaponController::class, 'show'] )
    ->name( 'weapons.show' )
    ->where( 'slug', '[a-zA-Z0-9-]+' );

// CONSUMABLES
Route::get( '/consumables', [App\Http\Controllers\ConsumableController::class, 'index'] )
    ->name( 'consumables.index' );
Route::get( '/consumables/{consumable:slug}', [App\Http\Controllers\ItemController::class, 'show'] )
    ->name( 'consumables.show' )
    ->where( 'slug', '[a-zA-Z0-9-]+' );
// ALT ROUTE -- FOOD
Route::get( '/food', [App\Http\Controllers\ConsumableController::class, 'index'] )
->name( 'food.index' );
Route::get( '/food/{consumable:slug}', [App\Http\Controllers\ConsumableController::class, 'show'] )
->name( 'food.show' )
->where( 'slug', '[a-zA-Z0-9-]+' );

// RECIPES
/*Route::get( '/recipes', [App\Http\Controllers\RecipeController::class, 'index'] )
    ->name( 'recipes.index' );
Route::get( '/recipes/{recipe:slug}', [App\Http\Controllers\RecipeController::class, 'show'] )
    ->where( 'slug', '[a-zA-Z0-9-]+' )
    ->name( 'recipes.show' );*/

/*
// STATUS EFFECTS
Route::get( '/status-effects', [App\Http\Controllers\StatusEffectController::class, 'index'] )
    ->name( 'status-effects.index' );
//*/

/* 
Route::resources(
    [
        'status-effects' => StatusEffectController::class,
        'recipes' => ItemRecipeController::class,
        'items' => ItemController::class,
    ],
    // options -- ['index', 'show', 'store', 'edit', 'update', 'destroy'];
    [
        // don't create routes for modifying the resources
        'only' => ['index', 'show']
    ]
); 
*/