<?php

namespace App\Providers;

use App\Converters\CraftingDeviceConverter;
use App\Converters\DataConverter;
use App\Http\Controllers\CraftingStationController;
use App\Http\Controllers\PieceTableController;
use App\Http\Controllers\StatusEffectController;
use App\Models\CraftingStation;
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
        $this->app->when(CraftingStationController::class)
            ->needs(CraftingDeviceConverter::class)
            ->give(function(){
                return new CraftingDeviceConverter(CraftingStation::class);
            });

        $this->app->when(PieceTableController::class)
            ->needs(CraftingDeviceConverter::class)
            ->give(function(){
                return new CraftingDeviceConverter(PieceTable::class);
            });
        // is not working, though above are working.....
        $this->app->when(StatusEffectController::class)
            ->needs(DataConverter::class)
            ->give(function(){
                return new DataConverter(StatusEffect::class);
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
            CraftingDeviceConverter::class
        ];
    }
}
