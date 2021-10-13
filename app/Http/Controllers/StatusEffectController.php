<?php

namespace App\Http\Controllers;

use App\Models\StatusEffect;
use Illuminate\Http\Request;

class StatusEffectController extends Controller
{
    use DoesConversion;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statusEffects = StatusEffect::all();

        return view( 'home', compact( 'statusEffects' ) );
    }

}
