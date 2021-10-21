<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Items\Armor;
use Illuminate\Http\Request;

class ArmorController extends Controller
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
        $page = $request->page ?? 1;
        $per_page = 32;

        $paginator = Armor::orderBy('name', 'asc')->paginate($per_page);

        $armor = $paginator->items();

        return view('items.armor.index',
                    compact(
                        'armor',
                        'paginator'
                    )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Models\Items\Craftables\Items\Armor $armor
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Armor $armor)
    {
        $armor->name = ucwords($armor->name);
        // lazy eager load recipe
        $armor->load('recipes', 'recipes.requirements', 'recipes.requirements.item');

        return view('items.armor.show', compact('armor'));
    }
}