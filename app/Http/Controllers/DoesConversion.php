<?php

namespace App\Http\Controllers;

use App\Converters\Converter;
use Illuminate\Http\Request;

trait DoesConversion
{
    protected Converter $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        $this->converter->convert();
    }
}