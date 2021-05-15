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
        $recipes = Recipe::with('requirements')->with('craftingStation')->with('item')->with('item.sharedData')->orderBy('name', 'asc')->get()->map(function ($recipe) {
            $recipe->name = ucwords($recipe->name);
            $recipe->requirements = $recipe->requirements->sortByDesc('name', SORT_NATURAL|SORT_FLAG_CASE)->sortByDesc('amount', SORT_NUMERIC)->all();
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
        $recipe = Recipe::with('requirements')->with('craftingStation')->with('item')->with('item.sharedData')->findOrFail($id);

        $recipe->name = ucwords($recipe->name);
        $recipe->requirements = $recipe->requirements->sortByDesc('name', SORT_NATURAL|SORT_FLAG_CASE)->sortByDesc('amount', SORT_NUMERIC)->all();

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
        $recipe = Recipe::where('slug', $slug)->firstOrFail();

        $recipe->name = ucwords($recipe->name);
        $recipe->requirements = $recipe->requirements->sortByDesc('name', SORT_NATURAL|SORT_FLAG_CASE)->sortByDesc('amount', SORT_NUMERIC)->all();

        return view('recipes.show', compact('recipe'));
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
