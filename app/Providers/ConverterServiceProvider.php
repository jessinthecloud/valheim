<?php

namespace App\Providers;

use App\Converters\Converter;
use App\Converters\CraftingDeviceConverter;
use App\Converters\DataParser;
use App\Converters\ItemConverter;
use App\Converters\ItemRecipeConverter;
use App\Converters\JsonSerializer;
use App\Converters\ModelConverter;
use App\Http\Controllers\CraftingStationController;
use App\Http\Controllers\DoesConversion;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\PieceRecipeController;
use App\Http\Controllers\PieceTableController;
use App\Http\Controllers\ItemRecipeController;
use App\Http\Controllers\StatusEffectController;
use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\ItemRecipe;
use App\Models\Piece;
use App\Models\PieceRecipe;
use App\Models\PieceTable;
use App\Models\StatusEffect;
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
        $this->app->when(StatusEffectController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ModelConverter();
            });
        $this->app->when(StatusEffectController::class)
            ->needs('$class')
            ->give(StatusEffect::class);
             
        $this->app->when(CraftingStationController::class )
            ->needs(Converter::class)
            ->give(function(){
                return new CraftingDeviceConverter(CraftingStation::class);
            });
        $this->app->when(CraftingStationController::class)
            ->needs('$class')
            ->give(CraftingStation::class);

        $this->app->when(PieceTableController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new CraftingDeviceConverter(PieceTable::class);
            });
        $this->app->when(PieceTableController::class)
            ->needs('$class')
            ->give(PieceTable::class);
            

        $this->app->when(ItemController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ItemConverter(Item::class);
            });

        $this->app->when( ItemController::class )
            ->needs('$class')
            ->give(Item::class);

        $this->app->when( ItemRecipeController::class )
            ->needs(Converter::class)
            ->give(function(){
                return new ItemRecipeConverter();
            });

        $this->app->when( ItemRecipeController::class )
            ->needs('$class')
            ->give(ItemRecipe::class);

        $this->app->when(PieceController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ModelConverter(Piece::class);
            });
        $this->app->when(PieceController::class)
            ->needs('$class')
            ->give(Piece::class);

        $this->app->when(PieceRecipeController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ModelConverter(Piece::class);
            });
        $this->app->when(PieceRecipeController::class)
            ->needs('$class')
            ->give(PieceRecipe::class);
        
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
            ModelConverter::class,
            CraftingDeviceConverter::class,
            ItemConverter::class,
            ItemRecipeConverter::class,
            ItemRecipe::class,
            Item::class,
            CraftingStation::class,
            StatusEffect::class,
            Piece::class,
            PieceTable::class,
            PieceRecipe::class,
        ];
    }
}
