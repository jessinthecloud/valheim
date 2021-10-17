<?php

use Illuminate\Support\Facades\Route;

if ( config( 'app.env' ) === 'local' ) {
    Route::prefix( 'convert' )
        ->name( 'convert.' )
        ->group( function () {
            /*Route::get(
            '/',
            [App\Http\Controllers\Conversion\ConvertController::class, 'index']
            )->name('index');*/

            Route::get(
                '/crafting-station',
                [App\Http\Controllers\Conversion\CraftingStationController::class, 'convert']
            )->name( 'crafting-station' );

            Route::get(
                '/status-effect',
                [App\Http\Controllers\Conversion\StatusEffectController::class, 'convert']
            )->name( 'status-effects' );

            Route::get(
                '/piece-table',
                [App\Http\Controllers\Conversion\PieceTableController::class, 'convert']
            )->name( 'piece-tables' );

            Route::get(
                '/items',
                [App\Http\Controllers\Conversion\ItemController::class, 'convert']
            )->name( 'items' );
// TODO:
            Route::get(
                '/piece',
                [App\Http\Controllers\Conversion\PieceController::class, 'convert']
            )->name( 'pieces' );

            /*Route::get(
            '/pieces/chunk/{offset}',
            [App\Http\Controllers\Conversion\ConvertController::class, 'pieces']
            )
            ->where('offset', '[0-9]+')
            ->name('pieces.chunk');*/

// TODO:
            Route::get(
                '/recipes',
                [App\Http\Controllers\Conversion\ItemRecipeController::class, 'convert']
            )->name( 'recipes' );

// TODO: piece-recipes, or combine with Pieces convert. OR combine with ItemRecipes and use a RecipesController?
            Route::get(
                '/piece-recipe',
                [App\Http\Controllers\Conversion\PieceRecipeController::class, 'convert']
            )->name( 'piece-recipes' );
            /*Route::get(
            '/{name}',
            [App\Http\Controllers\Conversion\ConvertController::class, 'convert']
            )->name('names');*/
        } );
}