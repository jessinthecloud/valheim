<?php

namespace App\Http\Controllers;

use App\Models\Armor;
use App\Models\Consumable;
use App\Models\Item;
use App\Models\Weapon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ItemController extends Controller
{
    use DoesConversion;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $per_page = 32;

        $weapons = Weapon::orderBy('name', 'asc')->get();
        $armors = Armor::orderBy('name', 'asc')->get();
        $consumables = Consumable::orderBy('name', 'asc')->get();

        $all_items = collect()
            ->concat($weapons)
            ->concat($armors)
            ->concat($consumables)
            ->sortBy('name')
            ;
            
        $items = $all_items->skip((($page-1) * $per_page))->take($per_page);

        $paginator = new LengthAwarePaginator($items, $all_items->count(), $per_page, $page, ['path'=>$request->getPathInfo()]);
    
    
        return view('items.index',
            compact(
                'items',
                'paginator'
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item $item
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Item $item)
    {
        $item->name = ucwords($item->name);
        // lazy eager load recipe
        $item->load('recipes', 'recipes.requirements', 'recipes.requirements.item');

        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item $item
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        //
    }
}
