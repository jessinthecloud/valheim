<?php

namespace App\Http\Controllers\Conversion;

use App\Converters\Converter;
use App\Converters\DataParser;
use App\Converters\JsonSerializer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait DoesConversion
{
    /**
     * @var \App\Converters\Converter 
     */
    protected Converter $converter;
    
    /**
     * @var \App\Converters\JsonSerializer
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
        $this->path = storage_path(config('filesystems.json_path'));
        $this->class = $class;
        
        $this->serializer = $serializer;
        $this->parser = $parser;
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        $filename = (Str::afterLast($request->getPathInfo(), '/') === 'piece-recipe') ? 'piece-list.json' : Str::singular(Str::afterLast($request->getPathInfo(), '/')).'-list.json';
        $contents = file_get_contents($this->path.$filename) ?? '';
        $data = $this->serializer->decode($contents);
        // make sure json is unique, e.g., crafting station
        $data = !empty(collect($data)->pluck('true_name')) ? collect($data)->unique('true_name')->all() : (!empty(collect($data)->pluck('raw_name')) ? collect($data)->unique('raw_name')->all() : $data);
       
        // change the collection's items
        $data = collect($data)->map(function($entity){
            //ddd($entity);
            $converted = $this->converter->convert( $entity, $this->class, $this->parser);
//            dump('CONVERTED: ',$converted, '+++++================================+++++');
            return $converted;
        })->filter();
        
        ddd($data);
    }
}