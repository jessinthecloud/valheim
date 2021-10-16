<?php

namespace App\Converters;

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
        $entity['slug'] = $entity['item_slug'] = isset($entity['slug']) ? 
            $parser->checkAndSetSlug($entity['slug'], $class) : 
            null;
       
        if( empty($entity['slug']) ){
            // if no slug, don't bother
            return null;
        }

        // only try to insert columns that exist
        $table = $parser->parseTable($class);
        $db_column_values = Arr::only($entity, Schema::getColumnListing($table));

        // create model
        // check if already exists
        // find existing or create model from values
        $model = $class::firstOrCreate(
        // array of unique key value to check 
            ['slug' => $entity['slug']],
            // array of values to use
            $db_column_values
        );

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
            
                // need to send array to the convert function
                if(!is_array($relation)){
                   
                    if($relation_method === 'creation'){

/*if(Str::contains($class, 'Recipe') && $relation_method === 'creation'){
//$model->refresh();
    dump($model::RELATION_INDICES, $model, $relation, $relations, 'related class: '.$relation_class.' relationship method: '.$relation_method.'() -- save function: '.$attach_function);
}*/
                    
                        // find existing item b/c only item_slug exists
                        $related = $relation_class::where('slug', $relations['item_slug'])->first();
                        // attach relation to model
                        isset($related) ? 
                            $this->attachRelated($model, $related, $relation_method, $attach_function) : 
                            $related = null;
                        
                        return $related;
/*if(isset($related)){
    dump($model::RELATION_INDICES, $model, $relation, $relations, 'related class: '.$relation_class.' relationship method: '.$relation_method.'() -- save function: '.$attach_function);
    ddd($relation_class::where('slug', $relations['item_slug'])->first()->toArray(), '===============--------===================');
}*/                  
                    } // end creation()
                    
                    // convert & attach relation to model
                    return $this->convertAndAttachRelation($model, $relations, $relation_class, $relation_method, $attach_function, $parser);
                }
                
                /*
                // handle 2D+ relation array (requirements)
                if( null !== collect($relation)->first() && !is_scalar( collect($relation)->first() ) ) {
                    return collect( $relation )->map(
                        function ( $entity ) use ( $relation, $relation_class, $relation_method, $parser, $model, $attach_function ) {
                            
                            // convert & attach relation to model
                            return $this->convertAndAttachRelation($model, $entity, $relation_class, $relation_method, $attach_function, $parser);
                        } 
                    );
                }

                // convert & attach relation to model
                return $this->convertAndAttachRelation($model, $relation, $relation_class, $relation_method, $attach_function, $parser); 
                */
//----------------------------------------------------------------
                // make sure $data is more than 1 dimensional before looping
                // otherwise, make $data an array and convert its names directly 
                // check all, not just first
                $flat_relation_data = collect($relation)->filter(function($entity){
                    return !is_array($entity);
                });

                $multi_relation_data = collect($relation)->diffAssoc($flat_relation_data);
                
                $multi_relation_data = $multi_relation_data->map(function($entity) use ( $relation, $relation_class, $relation_method, $parser, $model, $attach_function ) {

                    // convert & attach relation to model
                    return $this->convertAndAttachRelation($model, $entity, $relation_class, $relation_method, $attach_function, $parser);
                });

                $flat_relation_data = $this->convertAndAttachRelation($model, $relation, $relation_class, $relation_method, $attach_function, $parser);

                return $multi_relation_data->merge($flat_relation_data)->all();
            });
        }
/*if(Str::contains($class, 'Recipe')){
    dump($model, $model->creation, '--------');
}*/
        /*if( Str::contains($class, 'Recipe') && (defined($class.'::RELATION_INDICES') && in_array('item_slug', array_keys($class::RELATION_INDICES)))){
            dump($entity, $class::RELATION_INDICES);
        }*/
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
    protected function convertAndAttachRelation(Model $model, array $relation_data, string $relation_class, string $relation_method, string $attach_function, DataParser $parser) 
    {
//dump('convert related '.$relation_class.': ', $relation_data);
    
        $related = $this->convert(
            // relation data
            $relation_data,
            // relation's class
            $relation_class,
            // parser object
            $parser
        );
        
//dump('attach '.$relation_class.' to '.get_class($model).' ('.$model->slug.') with '.get_class($model).'->'.$relation_method.'()->'.$attach_function.'()', $related);

        // if null, quit
        if(empty($related)){
            return null;
        }

        // attach relation to model
        $this->attachRelated($model, $related, $relation_method, $attach_function);
        
/*if($relation_method === 'creation'){
    ddd($relation_method.'() attached?', $model, ($relation_method !== 'statusEffects' ? $model->$relation_method : ''));
}*/        

//dump('================');

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
        if(null === $model->$relation_method()) {
//dump('NO RELATION METHODS: '.$relation_method);        
            // no relation methods
            return;
        }
        
        if(is_array($model->$relation_method())){
            // some models have multiple methods for a related model class (SharedData -> StatusEffect)
            
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
        
        $model->$relation_method()->$attach_function($relation);
        // attach saves by default
        if($attach_function !== 'attach'){
            $model->save();
        }
    }     
}