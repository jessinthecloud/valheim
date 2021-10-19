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

// ITEMS
Route::get( '/items', [App\Http\Controllers\ItemController::class, 'index'] )
    ->name( 'items.index' );
Route::get( '/items/{item:slug}', [App\Http\Controllers\ItemController::class, 'show'] )
    ->name( 'items.show' )
    ->where( 'slug', '[a-zA-Z0-9-]+' );

// RECIPES
Route::get( '/recipes', [App\Http\Controllers\RecipeController::class, 'index'] )
    ->name( 'recipes.index' );
Route::get( '/recipes/{recipe:slug}', [App\Http\Controllers\RecipeController::class, 'showSlug'] )
    ->where( 'slug', '[a-zA-Z0-9-]+' )
    ->name( 'recipes.show' );

// PIECES
Route::get( '/pieces', [App\Http\Controllers\PieceController::class, 'index'] )
    ->name( 'pieces.index' );
Route::get( '/pieces/{piece:slug}', [App\Http\Controllers\PieceController::class, 'show'] )
    ->where( 'id', '[a-zA-Z0-9-]+' )
    ->name( 'pieces.show' );

// STATUS EFFECTS
Route::get( '/status-effects', [App\Http\Controllers\StatusEffectController::class, 'index'] )
    ->name( 'status-effects.index' );

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