<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use DoesConversion;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $paginator = Item::orderBy('name', 'asc')->paginate(32);

        $items = collect($paginator->items());

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
