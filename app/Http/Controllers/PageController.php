<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Item;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // flag as case insensitive
        $recipes = Recipe::with($this->relationsSubQuery())->orderBy('name', 'asc')->get()->map(function ($recipe) {
            return $recipe = $this->formatForView($recipe);
        });
        $is_listing = true;
        return view('home', compact('recipes', 'is_listing'));
    }

    protected function relationsSubQuery()
    {
        return [
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ];
    }

    protected function formatForView($recipe)
    {
        $recipe->name = ucwords($recipe->name);
        $recipe->max_quality = $recipe->item->sharedData->max_quality ?? 1;

        return $recipe;
    }
}
