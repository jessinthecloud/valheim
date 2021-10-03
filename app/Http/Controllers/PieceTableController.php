<?php

namespace App\Http\Controllers;

use App\Converters\CraftingDeviceConverter;
use Illuminate\Http\Request;

class PieceTableController extends CraftingDeviceController
{
    public function convert(Request $request)
    {
        parent::convert($request);
        
        echo "CONVERT PIECE TABLES";

    }
}