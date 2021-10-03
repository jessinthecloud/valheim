<?php

namespace App\Http\Controllers;

use App\Converters\CraftingDeviceConverter;
use Illuminate\Http\Request;

class CraftingStationController extends Controller
{
    protected CraftingDeviceConverter $converter;
    
    public function __construct(CraftingDeviceConverter $converter)
    {
        $this->converter = $converter;
    }
    
    public function convert(Request $request)
    {
        echo "CONVERT CRAFTING STATIONS";

        $this->converter->convert();
    }
}
