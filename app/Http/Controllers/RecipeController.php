<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipes = Recipe::with([
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ])->orderBy('name', 'asc')->get()->map(function ($recipe) {
            $recipe->name = ucwords($recipe->name);
        });

        return view('recipes.index', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = Recipe::with([
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ])->findOrFail($id);

        $recipe->name = ucwords($recipe->name);

        return view('recipes.show', compact('recipe'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */
    public function showSlug($slug)
    {
        $recipe = Recipe::with([
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ])->where('slug', $slug)->firstOrFail();

        $recipe->name = ucwords($recipe->name);
        $max_quality = $recipe->item->sharedData->max_quality;
        $recipe->max_quality = $max_quality;
        $totals = [];
        $upgrades = [];
        // start at the first upgrade level and determine the required item amounts
        for ($i=2; $i<=$max_quality; $i++) {
            foreach ($recipe->requirements as $req) {
                $upgrades[$i][$req->name]= $req->getAmount($i);
                $sum = array_sum(array_column($upgrades, $req->name));

                dump("amount: {$req->amount}, sum: $sum");

                $totals [$req->name]= '<strong>'.($sum+$req->amount).'</strong>';
            } // end foreach
        } // end for

        $totals = urldecode(str_replace('=', ' ', http_build_query(array_flip($totals), null, ', ')));

        dump($recipe);
        dump("max level: ".$recipe->item->sharedData->max_quality);
        dump($upgrades);
        dump($totals);

        return view('recipes.show', compact('recipe', 'upgrades', 'totals'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}
