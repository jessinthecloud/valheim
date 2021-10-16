<?php

namespace App\Converters;

use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\RepairStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelConverter implements Converter
{    
    /**
     * Parse fields, create model and attach related models 
     * 
     * @param array                      $data
     * @param string                     $class
     * @param \App\Converters\DataParser $parser
     *
     * @return mixed
     */
    public function convert(array $data, string $class, DataParser $parser)
    {
        // create/convert names
        $entity = $parser->parse($data, $class);
        // make sure slug is set and unique
        $entity['slug'] = $entity['item_slug'] = $entity['piece_slug'] =isset($entity['slug']) ? 
            $parser->checkAndSetSlug($entity['slug'], $class) : 
            null;
//dump(' ---- convert() '.$class.' :::>> '.($data['raw_name'] ?? $data['true_name'] ?? $data['var_name'] ?? '').' ---- ', $data);

        if( empty($entity['slug']) ){
//dump(' == SLUG NULL SKIP == ');        
            // if no slug, don't bother
            return null;
        }        

        // only try to insert columns that exist
        $table = $parser->parseTable($class);
        $db_column_values = Arr::only($entity, Schema::getColumnListing($table));
        
        // requirements also need to check amount and per level 
        // because slug is not unique to Requirement
        $unique_fields = (Str::contains($class, ["Requirement"])) ? $db_column_values : ['slug' => $entity['slug']];
        
        // create model
        // check if already exists
        // find existing or create model from values
        $model = $class::firstOrCreate(
        // array of unique key value to check 
            $unique_fields,
            // array of values to use
            $db_column_values
        );
        
/*if(Str::contains($class, ["Requirement"])) {
    dump(
        $class.' -- '.$parser->parseTable($class),
        'columns: ',
        Schema::getColumnListing($table),
        'column vals: ',
        $db_column_values,
        'entity',
        $entity,
        'model',
        $model
    );
}*/

        if(defined($class.'::RELATION_INDICES')) {
            // get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with
            // PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $class::RELATION_INDICES );

            // convert relations
            $relations = collect($relations)->map(function($relation, $key) use ($model, $parser, $entity, $class, $relations) {
            
                // $key is the unique array index / DB column            
                $relation_class = $class::RELATION_INDICES[$key]['class'];
                $relation_method = $class::RELATION_INDICES[$key]['method'];
                // determine relation attach function attach() vs associate()
                $attach_function = $class::RELATION_INDICES[$key]['relation'];
                
//if(Str::contains(get_class($model), 'Piece')) {
/* 
if(Str::contains(Str::lower($model->name), 'cauldron')) {
    dump(
        $class::RELATION_INDICES,
        $entity,
        $relation,' --- ',
        $relations,
        'related class: ' . $relation_class . ' relationship method: ' . $relation_method . '() -- save function: ' . $attach_function
    );
}  
*/     

                // need to send array to the convert function
                if(!is_array($relation)){
//dump('relation not array');                   
                    if($relation_method === 'creation'/* || isset($relations['piece_slug'])*/){
                                            
                        // find existing item b/c only item_slug/piece_slug exists
                        // when trying to find related
                        $related = (isset($relations['item_slug']) 
                            ? $relation_class::where('slug', $relations['item_slug'])->first() 
                            : null) 
                        ?? (isset($relations['piece_slug']) 
                            ? $relation_class::where('slug', $relations['piece_slug'])->first() 
                            // sometimes recipes don't have item slug that matches 
                            : null) 
                        ?? (isset($relations['item_slug']) 
                            ? $relation_class::where(
                                    'name', 
                                    Str::replace('-', ' ', $relations['item_slug'])
                                )->first() 
                            : null) 
                        ?? ((null !== $model->name)
                            ? $relation_class::where(
                                    'name',
                                    $model->name
                                )->first()
                            : null);
/*if(isset($relations['item_slug']) && $relations['item_slug'] === 'onionsoup'){
    dump($relation_class::where(
        'name',
        $model->name
    )->first(), 'model name: '.$model->name.', item slug: '.($relations['item_slug'] ?? '').', name: '.($relations['name'] ?? ''), $relations, 'found: ', $related, 'model: ', $model, 'entity: ', $entity);
}*/                        
                        // attach relation to model
                        isset($related) ? 
                            $this->attachRelated($model, $related, $relation_method, $attach_function) : 
                            $related = null;
                        
                        return $related;
                    } // end creation() or piece_slug
                    
                    // convert & attach relation to model
                    return $this->convertAndAttachRelation($model, $relations, $relation_class, $relation_method, $attach_function, $parser, $entity);
                }
                
                // make sure $data is more than 1 dimensional before looping
                // otherwise, make $data an array and convert its names directly 
                // check all, not just first
                $flat_relation_data = collect($relation)->filter(function($entity){
                    return !is_array($entity);
                });

                $multi_relation_data = collect($relation)->diffAssoc($flat_relation_data);
                
                $multi_relation_data = $multi_relation_data->map(function($entity) use ( $relation, $relation_class, $relation_method, $parser, $model, $attach_function ) {
//dump('multi-d relation', $entity);
                    // convert & attach relation to model
                    return $this->convertAndAttachRelation($model, $entity, $relation_class, $relation_method, $attach_function, $parser, $entity);
                });

                $flat_relation_data = $this->convertAndAttachRelation($model, $relation, $relation_class, $relation_method, $attach_function, $parser, $entity);

                return $multi_relation_data->merge($flat_relation_data)->all();
            });
        }
        
        return $model;
        
    } // end convert()

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $relation_data
     * @param string                              $relation_class   class to attach
     * @param string                              $relation_method  model relationship method
     * @param string                              $attach_function  function that attaches
     *                                                              this kind of relationship to model
     * @param \App\Converters\DataParser          $parser
     *
     * @return mixed
     * @throws \Exception
     */
    protected function convertAndAttachRelation(Model $model, array $relation_data, string $relation_class, string $relation_method, string $attach_function, DataParser $parser, array $entity) 
    {
//dump('convertAndAttach() '.$relation_class);

        // requirements should not convert their relation (item), only find existing and attach
        if($relation_method === 'item'){
            $related = Item::firstWhere('slug', $entity['slug']);
//    ddd($slug, $model, $relation_data, $related);
            if(isset($related)){
                $this->attachRelated($model, $related, $relation_method, $attach_function);
            }
            return $related;
        }
        // piece recipe should not convert crafting station, only find existing and attach
        if($relation_method === 'craftingStation' 
            || ($relation_method === 'craftingDevice' 
                && Str::contains($relation_class, 'CraftingStation')) 
            || ($relation_method === 'repairStation' 
                && Str::contains($relation_class, 'RepairStation'))
        ){
        
            $related = CraftingStation::firstWhere('raw_name', $entity['raw_crafting_station_name']) ?? RepairStation::firstWhere('raw_name', $entity['raw_repair_station_name']) ?? null;

//dump($entity, $model, $relation_data, $related);

            if(isset($related)){
                $this->attachRelated($model, $related, $relation_method, $attach_function);
            }
            return $related;
        }
    
        $related = $this->convert(
            // relation data
            $relation_data,
            // relation's class
            $relation_class,
            // parser object
            $parser
        );

        // if null, quit
        if(empty($related)){
            return null;
        }
        
/* 
if(Str::contains(get_class($model), 'Piece')) {
    dump(
        'attach ' . $relation_class . ' ('.(isset($related) ? $related->slug : 'NO SLUG').') to ' . get_class( $model ) . ' (' . $model->slug . ') with ' . get_class(
            $model
        ) . '->' . $relation_method . '()->' . $attach_function . '('.(isset($related) ? get_class($related) : '').')'
//        $model,
//        $related
    );
} 
*/
        
        // attach relation to model
        $this->attachRelated($model, $related, $relation_method, $attach_function);

/* 
if(Str::contains(Str::lower($model->name), 'cauldron') || Str::contains(Str::lower($related->name), 'cauldron')) {
    dump(
        $relation_method.'() attached?', 
        $model->$relation_method, 
        'related', $related, 
        'model', $model
    );
dump('================');
}
*/


        return $related;
    }

    /**
     * Find and save relation to model 
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Database\Eloquent\Model $relation
     * @param string                              $relation_method
     * @param string                              $attach_function
     *
     * @return void
     */
    protected function attachRelated(Model $model, Model $relation, string $relation_method, string $attach_function)
    {
//dump('attachRelated() '.get_class($model).'->'.$relation_method.'()->'.$attach_function.'('.get_class($relation).')', $model, $relation);
    
        if(null === $model->$relation_method()) {
//dump('NO RELATION METHODS: '.$relation_method);        
            // no relation methods
            return;
        }
        
        if(is_array($model->$relation_method())){
            // some models have multiple methods for a related model class (SharedData -> StatusEffect)
/*dump('RELATION METHOD RETURNS ARRAY');
dump(get_class($model).'->'.$relation_method.'()->'.$attach_function.'('.get_class($relation).')');*/

            collect($model->$relation_method())->each(function($method) use ($model, $relation, $attach_function) {
                // don't use ALL methods to attach,
                // just the one with matching type (e.g., status effect)
                if(isset($relation['type']) && Str::startsWith($method, $relation['type'])){
                    $model->$method()->$attach_function($relation);
                }
            });

            // attach saves by default
            if($attach_function !== 'attach'){
                $model->save();
            }
            
            return;         
        }
//dump(get_class($model).'->'.$relation_method.'()->'.$attach_function.'('.get_class($relation).')');        
        $model->$relation_method()->$attach_function($relation);
        // attach saves by default
        if($attach_function !== 'attach'){
            $model->save();
        }
    }     
}