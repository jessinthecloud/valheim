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
        $recipes = Recipe::all()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);
        $items = Item::all()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE);

        return view('home', compact('recipes', 'items'));
    }
}
