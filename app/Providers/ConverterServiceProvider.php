<?php

namespace App\Providers;

use App\Converters\Converter;
use App\Converters\CraftingDeviceConverter;
use App\Converters\DataConverter;
use App\Converters\ItemConverter;
use App\Converters\ItemRecipeConverter;
use App\Http\Controllers\CraftingStationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PieceTableController;
use App\Http\Controllers\ItemRecipeController;
use App\Http\Controllers\StatusEffectController;
use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\PieceTable;
use App\Models\Recipe;
use App\Models\SharedData;
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
                return new DataConverter(StatusEffect::class);
            });
             
        $this->app->when(CraftingStationController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new CraftingDeviceConverter(CraftingStation::class);
            });

        $this->app->when(PieceTableController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new CraftingDeviceConverter(PieceTable::class);
            });

        $this->app->when(ItemController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ItemConverter(Item::class);
            });
        
        /*// inject into method
        $this->app->bindMethod(
            [ItemConverter::class, 'convertSharedData'],
            function($obj, $app){
                return new DataConverter(SharedData::class);
            }
        );*/
        
        /*$this->app->when( ItemConverter::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ItemRecipeConverter(Recipe::class);
            });*/
            
        $this->app->when( ItemRecipeController::class)
            ->needs(Converter::class)
            ->give(function(){
                return new ItemRecipeConverter(Recipe::class);
            });

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
            CraftingDeviceConverter::class,
            ItemConverter::class,
            DataConverter::class,
            ItemRecipeConverter::class,
        ];
    }
}
