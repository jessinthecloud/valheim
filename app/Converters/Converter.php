<?php

namespace App\Converters;

interface Converter
{
    public function convert(array $data, string $class, DataParser $parser);
}