<?php

namespace App\Http\Controllers;

use App\Models\Craftables\Item;
use App\Models\Craftables\Piece;
use Illuminate\Http\Request;

class CraftableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $per_page = 32;

        $paginator = Item::select('id','name','slug', 'url_path')
            ->union(Piece::select('id','name','slug', 'url_path'))
            ->orderBy('name', 'asc')
            ->paginate($per_page);
        
        $items = $paginator->items();

        /*$all_items = $items->concat($pieces)->sortBy('name');
           
        $items = $all_items->skip((($page-1) * $per_page))->take($per_page);

        $paginator = new LengthAwarePaginator($items, $all_items->count(), $per_page, $page, ['path'=>$request->getPathInfo()]);*/

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
     * @param \Illuminate\Http\Request    $request
     * @param \App\Models\Craftables\Item $item
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
}