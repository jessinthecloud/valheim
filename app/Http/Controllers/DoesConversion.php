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
    protected Converter $converter;
    
    /**
     * @var \App\JsonSerializer
     */
    protected JsonSerializer $serializer;
    
    /**
     * @var \App\Converters\DataParser
     */
    protected DataParser $parser;
    
    private string $path;

    public function __construct(DataParser $parser, Converter $converter, JsonSerializer $serializer)
    {
        $this->path = config('filesystems.json_path');
        $this->serializer = $serializer;
        $this->parser = $parser;
        $this->converter = $converter;
    }

    public function convert(Request $request)
    {
        $filename = Str::singular(Str::afterLast($request->getPathInfo(), '/')).'-list.json';
        $contents = file_get_contents($this->path.'/'.$filename) ?? '';
        $data = $this->serializer->decode($contents);
        
        // directly change the collection's items
        $data = collect($data)->map(function($entity, $key){
            $entity = $this->parser->parse($entity);
            $entity['slug'] = isset($entity['slug']) ? $this->converter->checkAndSetSlug($entity['slug']) : null;

            // only try to insert columns that exist
            $existing_values = Arr::only($entity, Schema::getColumnListing($this->converter->table));
            // initialize model object with values 
            $model = new $this->converter->class($existing_values);
            
            if(defined($this->converter->class.'::RELATION_INDICES')) {
                // get any that are also relationships that need to be mapped
                // use intersect to compare by keys and avoid issue with
                // PHP trying to compare multidimensional values
                $relations = array_intersect_key( $entity, $this->converter->class::RELATION_INDICES );

                $relations = $this->parser->parse($relations); 
                
                // how best to continue recursively?
                
                // TODO: convert relations
                // need to know convert class to use for specific relation model
                
                                
                $entity = array_merge($entity, $relations->all());
                
                // TODO: check if relation is already attached?
            }
//ddd($entity);

            return $entity;
        });
        
        ddd($data);
    }
}