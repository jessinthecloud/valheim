<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Items\Weapon;
use Illuminate\Http\Request;

class WeaponController extends Controller
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

        $paginator = Weapon::orderBy('name', 'asc')->paginate($per_page);

        $weapons = $paginator->items();

        return view('items.weapons.index',
                    compact(
                        'weapons',
                        'paginator'
                    )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Models\Items\Craftables\Items\Weapon $weapon
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Weapon $weapon)
    {
        $weapon->name = ucwords($weapon->name);
        // lazy eager load recipe
        $weapon->load('recipes', 'recipes.requirements', 'recipes.requirements.item');

        return view('items.weapons.show', compact('weapon'));
    }
}