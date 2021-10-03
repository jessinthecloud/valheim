<?php

namespace App\Providers;

use App\Converters\CraftingDeviceConverter;
use App\Http\Controllers\CraftingStationController;
use App\Models\CraftingStation;
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
                return new CraftingDeviceConverter('CraftingStation');
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
