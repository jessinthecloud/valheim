<?php

namespace App\Http\Controllers;

use App\Converters\Converter;
use App\Converters\DataParser;
use App\JsonSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait DoesConversion
{
    /**
     * @var \App\Converters\Converter 
     */
    protected Converter $converter;
    
    /**
     * @var \App\JsonSerializer
     */
    protected JsonSerializer $serializer;
    
    /**
     * @var \App\Converters\DataParser
     */
    protected DataParser $parser;
    
    protected string $class;

    private string $path;

    public function __construct(DataParser $parser, Converter $converter, JsonSerializer $serializer, string $class)
    {
        $this->path = config('filesystems.json_path');
        $this->class = $class;
        
        $this->serializer = $serializer;
        $this->parser = $parser;
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        $filename = Str::singular(Str::afterLast($request->getPathInfo(), '/')).'-list.json';
        $contents = file_get_contents($this->path.'/'.$filename) ?? '';
        $data = $this->serializer->decode($contents);
        
        // change the collection's items
        $data = collect($data)->map(function($entity){
            //ddd($entity);
            return $this->converter->convert( $entity, $this->class, $this->parser);
        });
        
        ddd($data);
    }
}