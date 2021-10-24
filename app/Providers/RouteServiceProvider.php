<?php

namespace App\Providers;

use App\Models\Items\Craftables\Items\Armor;
use App\Models\Items\Craftables\Items\Consumable;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Craftables\Items\Weapon;
use App\Models\Items\Craftables\Pieces\Piece;
use App\Models\Items\NaturalItem;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
        
        // Custom bind Item to route
        // get a specific item type instead of generic Item 
        Route::bind('item', function ($slug) {
            $item = Armor::where('slug', $slug)->first()
                ?? Weapon::where('slug', $slug)->first()
                ?? Consumable::where('slug', $slug)->first()
                ?? CraftableItem::where('slug', $slug)->first() 
                ?? NaturalItem::where('slug', $slug)->first() 
                /* 
                ?? Furniture::where('slug', $slug)->first()
                ?? BuildingPiece::where('slug', $slug)->first()
                ?? CraftingPiece::where('slug', $slug)->first() 
                */
                ?? Piece::where('slug', $slug)->first() 
                ?? abort('404');
//ddd($item);
            return $item;
        });

        // Custom bind Recipe to route
        // get a specific recipe type instead of generic Recipe 
        Route::bind('recipe', function ($slug) {
            $recipe = ItemRecipe::where('slug', $slug)->first()
            ?? PieceRecipe::where('slug', $slug)->first()
            ?? abort('404');
//ddd($recipe);
            return $recipe;
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
