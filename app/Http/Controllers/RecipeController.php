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
        $recipes = Recipe::all();

        return view('home', compact('recipes'));
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
        $recipe = Recipe::findOrFail($id);

        return view('home', compact('recipe'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */
    public function showSlug($slug)
    {
        $recipe = Recipe::where('slug', $slug)->first();

        return view('home', compact('recipe'));
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

/**
* PHP's DOM classes are recursive but don't provide an implementation of
* RecursiveIterator. This class provides a RecursiveIterator for looping over DOMNodeList
*/
class DOMNodeRecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function __construct(\DOMNodeList $node_list)
    {
        $nodes = array();
        foreach ($node_list as $node) {
            $nodes[] = $node;
        }

        parent::__construct($nodes);
    }

    public function getRecursiveIterator()
    {
        return new \RecursiveIteratorIterator($this, \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }


    public function getChildren()
    {
        return new self($this->current()->childNodes);
    }
}
