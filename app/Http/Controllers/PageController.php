<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Item;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('home');
    }
}
