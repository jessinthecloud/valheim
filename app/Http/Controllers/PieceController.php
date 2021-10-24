<?php

namespace App\Http\Controllers;

use App\Models\Items\Craftables\Pieces\Piece;
use Illuminate\Http\Request;

class PieceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index( Request $request )
    {
        $page = $request->page ?? 1;
        $per_page = 32;

        $paginator = Piece::selectRaw( 'id, name, slug, "piece" as type, "pieces" as url' )
            ->orderBy( 'name', 'asc' )
            ->paginate( $per_page );

        $items = $paginator->items();

        /*$all_items = $items->concat($pieces)->sortBy('name');
           
        $items = $all_items->skip((($page-1) * $per_page))->take($per_page);

        $paginator = new LengthAwarePaginator($items, $all_items->count(), $per_page, $page, ['path'=>$request->getPathInfo()]);*/

        return view(
            'pieces.index',
            compact(
                'items',
                'paginator'
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request                  $request
     * @param \App\Models\Items\Craftables\Pieces\Piece $piece
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show( Request $request, Piece $piece )
    {
        $piece->name = ucwords( $piece->name );
        // lazy eager load recipe
        $piece->load( 'recipes', 'recipes.requirements', 'recipes.requirements.item' );

        return view( 'items.show', compact( 'piece' ) );
    }
}