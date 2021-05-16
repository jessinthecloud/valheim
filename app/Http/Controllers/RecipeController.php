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
        $totals = [];
        $totals_strings = [];
        $upgrades = [];
        $recipes = Recipe::with([
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ])->orderBy('name', 'asc')->get()->map(function ($recipe) use (&$totals, &$upgrades, &$totals_strings) {
            $recipe->name = ucwords($recipe->name);
            $max_quality = $recipe->item->sharedData->max_quality ?? 1;
            $recipe->max_quality = $max_quality;

            // start at the first upgrade level and determine the required item amounts
            for ($i=2; $i<=$max_quality; $i++) {
                foreach ($recipe->requirements as $req) {
                    $upgrades[$i][$req->name]= $req->getAmount($i);
                    $sum = array_sum(array_column($upgrades, $req->name));
                    // dump("req:", $req, "totals: ", $totals);
                    $totals [$req->name]= '<strong>'.($sum+$req->amount).'</strong>';
                } // end foreach
            } // end for

            $totals_strings []= urldecode(str_replace('=', ' ', http_build_query(array_flip($totals), null, ', ')));

            // dump($recipe);
            // dump("max level: $max_quality");
            // dump($upgrades);
            // dump($totals);
            return $recipe;
        })->filter();

        // TODO take care of this for multiple recipes returned
        $totals = '';
        $upgrades = [];

        return view('recipes.index', compact('recipes', 'upgrades', 'totals'));
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
        $max_quality = $recipe->item->sharedData->max_quality;
        $recipe->max_quality = $max_quality;
        $totals = [];
        $upgrades = [];
        // start at the first upgrade level and determine the required item amounts
        for ($i=2; $i<=$max_quality; $i++) {
            foreach ($recipe->requirements as $req) {
                $upgrades[$i][$req->name]= $req->getAmount($i);
                $sum = array_sum(array_column($upgrades, $req->name));

                $totals [$req->name]= '<strong>'.($sum+$req->amount).'</strong>';
            } // end foreach
        } // end for

        $totals = urldecode(str_replace('=', ' ', http_build_query(array_flip($totals), null, ', ')));
        /*
                dump($recipe);
                dump("max level: ".$recipe->item->sharedData->max_quality);
                dump($upgrades);
                dump($totals);*/

        return view('recipes.show', compact('recipe', 'upgrades', 'totals'));
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
            $upgrades[$i]= [
                'station' => $recipe->getRequiredStation($i)->name,
                'station_level' => $recipe->getRequiredStationLevel($i),
            ];
            foreach ($recipe->requirements as $req) {
                $upgrades[$i]['resources'][$req->name]= $req->getAmount($i);
                $sum = array_sum(array_column($upgrades, $req->name));

                $totals [$req->name]= '<strong>'.($sum+$req->amount).'</strong>';
            } // end foreach
        } // end for

        $totals = urldecode(str_replace('=', ' ', http_build_query(array_flip($totals), null, ', ')));

        // dump($recipe);
        // dump("max level: ".$recipe->item->sharedData->max_quality);
        // dump($upgrades);
        // dump($totals);

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
