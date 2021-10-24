<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Items\Consumable;
use Illuminate\Http\Request;

class ConsumableController extends Controller
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

        $paginator = Consumable::orderBy('name', 'asc')->paginate($per_page);

        $consumables = $paginator->items();

        return view('items.consumables.index',
                    compact(
                        'consumables',
                        'paginator'
                    )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Models\Items\Craftables\Items\Consumable $consumable
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Consumable $consumable)
    {
        $consumable->name = ucwords($consumable->name);
        // lazy eager load recipe
        $consumable->load('recipes', 'recipes.requirements', 'recipes.requirements.item');

        return view('items.consumables.show', compact('consumable'));
    }
}