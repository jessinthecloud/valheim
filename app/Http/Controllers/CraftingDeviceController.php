<?php

namespace App\Http\Controllers;

use App\Converters\CraftingDeviceConverter;
use Illuminate\Http\Request;

abstract class CraftingDeviceController extends Controller
{
    protected CraftingDeviceConverter $converter;

    public function __construct(CraftingDeviceConverter $converter)
    {
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        $this->converter->convert();
    }
}