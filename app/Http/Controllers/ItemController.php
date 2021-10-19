<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Craftables\Pieces\Piece;
use App\Models\Items\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
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

        $paginator = Item::select('id','name','slug')
            ->unionAll(Piece::select('id','name','slug'))
            ->orderBy('name', 'asc')->get();
ddd(Piece::select('id','name','slug')->dd());            
//            ->paginate($per_page);
        
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
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Items\Item   $item
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