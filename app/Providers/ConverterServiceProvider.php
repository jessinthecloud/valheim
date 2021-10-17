<?php

namespace App\Providers;

use App\Converters\Converter;
use App\Converters\ModelConverter;
use App\Http\Controllers\Conversion\CraftingStationController;
use App\Http\Controllers\Conversion\ItemController;
use App\Http\Controllers\Conversion\PieceController;
use App\Http\Controllers\Conversion\PieceRecipeController;
use App\Http\Controllers\Conversion\PieceTableController;
use App\Http\Controllers\Conversion\ItemRecipeController;
use App\Http\Controllers\Conversion\StatusEffectController;
use App\Models\Craftables\Pieces\Piece;
use App\Models\Tools\CraftingStation;
use App\Models\Craftables\Items\Item;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Tools\PieceTable;
use App\Models\Craftables\Items\StatusEffect;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ConverterServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when( StatusEffectController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter();
            } );
        $this->app->when( StatusEffectController::class )
            ->needs( '$class' )
            ->give( StatusEffect::class );

        $this->app->when( CraftingStationController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter();
            } );
        $this->app->when( CraftingStationController::class )
            ->needs( '$class' )
            ->give( CraftingStation::class );

        $this->app->when( PieceTableController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter( PieceTable::class );
            } );
        $this->app->when( PieceTableController::class )
            ->needs( '$class' )
            ->give( PieceTable::class );


        $this->app->when( ItemController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter();
            } );

        $this->app->when( ItemController::class )
            ->needs( '$class' )
            ->give( Item::class );

        $this->app->when( ItemRecipeController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter();
            } );

        $this->app->when( ItemRecipeController::class )
            ->needs( '$class' )
            ->give( ItemRecipe::class );

        $this->app->when( PieceController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter( Piece::class );
            } );
        $this->app->when( PieceController::class )
            ->needs( '$class' )
            ->give( Piece::class );

        $this->app->when( PieceRecipeController::class )
            ->needs( Converter::class )
            ->give( function () {
                return new ModelConverter( Piece::class );
            } );
        $this->app->when( PieceRecipeController::class )
            ->needs( '$class' )
            ->give( PieceRecipe::class );
        /*// inject into method
        $this->app->bindMethod(
            [ItemConverter::class, 'convertSharedData'],
            function($obj, $app){
                return new JsonConverter(SharedData::class);
            }
        );*/
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() : array
    {
        return [
            StatusEffect::class,
            CraftingStation::class,
            PieceTable::class,
            Item::class,
            Piece::class,
            ItemRecipe::class,
            PieceRecipe::class,
            ModelConverter::class,
        ];
    }
}
