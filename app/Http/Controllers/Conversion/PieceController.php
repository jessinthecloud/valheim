<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use App\Models\Craftables\Pieces\Piece;
use Illuminate\Http\Request;

class PieceController extends Controller
{
    use DoesConversion;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $paginator = Piece::orderBy('name', 'asc')->paginate(32);
        $pieces = collect($paginator->items());

        return view('pieces.index',
            compact(
                'pieces',
                'paginator'
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Craftables\Pieces\Piece $piece
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Piece $piece)
    {
        $piece->name = ucwords($piece->name);
        $piece->load('craftingDevice', 'craftingStation', 'requirements');

        return view('pieces.show', compact('piece'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function edit(Piece $piece)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request            $request
     * @param \App\Models\Craftables\Pieces\Piece $piece
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Piece $piece)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Craftables\Pieces\Piece $piece
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Piece $piece)
    {
        //
    }
}
