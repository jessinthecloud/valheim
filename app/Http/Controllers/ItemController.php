<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Items\Armor;
use App\Models\Items\Craftables\Items\Consumable;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Craftables\Items\Weapon;
use App\Models\Items\Craftables\Pieces\Piece;
use App\Models\Items\Item;
use App\Models\Items\NaturalItem;
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
        // get items as their specific subtypes
        $paginator = Armor::selectRaw('id,name,slug,image, "items" as url')
            ->union(Weapon::selectRaw('id,name,slug,image, "items" as url'))
            ->union(Consumable::selectRaw('id,name,slug,image, "items" as url'))
            ->union(CraftableItem::selectRaw('id,name,slug,image, "items" as url'))
            ->union(NaturalItem::selectRaw('id,name,slug,image, "items" as url'))
            ->union(Piece::selectRaw('id, name, slug,image, "pieces" as url'))
            ->orderBy('name', 'asc')
            ->paginate($per_page)
            ;
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
     * @param \Illuminate\Http\Request                         $request
     * @param \App\Models\Items\Craftables\Items\Item $item
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, Item $item)
    {
//dump($item);    
        $item->name = ucwords($item->name);
        
        // lazy eager load recipe
        $item->load('recipes', 'recipes.requirements', 'recipes.requirements.item');
        
        /*$item->recipes->transform(function($recipe){
            $recipe->requirements->transform(function($requirement){
                dump($requirement, $requirement->item);
                return $requirement->item->filter();
            });
            return $recipe;
        });*/

        return view('items.show', compact('item'));
    }
}