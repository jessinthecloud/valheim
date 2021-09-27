<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use Illuminate\Http\Request;

class PieceController extends Controller
{
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
     * Display the specified resource.
     *
     * @param  \App\Models\Piece $piece
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $piece = Piece::with('pieceTable')->with('requirements')->findOrFail($id);
        $piece->name = ucwords($piece->name);

        return view('pieces.show', compact('piece'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Piece $piece
     * @return \Illuminate\Http\Response
     */
    public function showSlug($slug)
    {
        $piece = Piece::with('pieceTable')->with('requirements')->where('slug', $slug)->firstOrFail();
        $piece->name = ucwords($piece->name);

        return view('pieces.show', compact('piece'));
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Piece $piece)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Piece  $piece
     * @return \Illuminate\Http\Response
     */
    public function destroy(Piece $piece)
    {
        //
    }
}
