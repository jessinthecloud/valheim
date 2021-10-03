<?php

namespace App\Http\Controllers;

use App\Converters\DataConverter;
use App\Models\StatusEffect;
use Illuminate\Http\Request;

class StatusEffectController extends Controller
{
    protected DataConverter $converter;

    public function __construct(DataConverter $converter)
    {
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        echo "CONVERT STATUS EFFECT";

        $this->converter->convert();
    }
    
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
