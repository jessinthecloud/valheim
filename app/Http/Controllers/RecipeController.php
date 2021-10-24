<?php

namespace App\Http\Controllers;

use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $per_page = 32;

        $paginator = ItemRecipe::selectRaw('id,name,slug, "item" as type, "items" as url')
        ->unionAll(PieceRecipe::selectRaw('id, name, slug, "piece" as type, "pieces" as url'))
        ->orderBy('name', 'asc')
        ->paginate($per_page)
        ;
        $recipes = $paginator->items();

        return view('recipes.index',
            compact(
                'recipes',
                'paginator'
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request   $request
     * @param \App\Models\Recipes\Recipe $recipe
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Recipe $recipe)
    {
//dump($recipe);    
        $recipe->name = ucwords($recipe->name);

        // lazy eager load
        $recipe->load('creation', 'requirements', 'requirements.item');

        return view('recipes.show', compact('recipe'));
    }
}