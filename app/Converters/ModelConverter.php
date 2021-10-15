<?php

namespace App\Converters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelConverter implements Converter
{
    public function convert(array $data, string $class, DataParser $parser)
    {
        // create/convert names
        $entity = $parser->parse($data);
        $entity['slug'] = isset($entity['slug']) ? $parser->checkAndSetSlug($entity['slug'], $class) : null;

dump('entity',$entity);

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

dump('model', $model);

        if(defined($class.'::RELATION_INDICES')) {
            // get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with
            // PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $class::RELATION_INDICES );

//dump('relations', $relations, 'from', $entity, 'indices:',$class::RELATION_INDICES);

            // convert relations
            $relations = collect($relations)->map(function($relation, $key) use ($model, $parser, $entity, $class, $relations) {
            
dump('relation',$relation); 
            
                // $key is the unique array index / DB column            
                $relation_class = $class::RELATION_INDICES[$key]['class'];
                $relation_method = $class::RELATION_INDICES[$key]['method'];
                $relation_method = $class::RELATION_INDICES[$key]['method'];
                // determine relation attach function attach() vs associate()
                $attach_function = $class::RELATION_INDICES[$key]['relation'];
                
                // need to send array to the convert function
                if(!is_array($relation)){
                
                    $relation = $this->convert( 
                        // relation data
                        $relations,
                        // relation's class
                        $relation_class,
                        // parser object
                        $parser
                    );

                    // attach relation to model
                    // attach relation to model
                    $this->attachRelated($model, $relation, $relation_method, $attach_function);
                }
                // make sure to handle 2D+
                else if( null !== collect( $relation )->first() && !is_scalar( collect($relation)->first()) ) {
                    $relation = collect( $relation )->map(
                        function ( $entity ) use ( $relation, $relation_class, $relation_method, $parser, $model, $attach_function ) {
ddd($entity, $relation, collect( $relation )->first());                            
                            $entity = $this->convert(
                                // relation data
                                $entity,
                                // relation's class
                                $relation_class,
                                // parser object
                                $parser
                            );

                            // attach relation to model
                            // attach relation to model
                            $this->attachRelated($model, $entity, $relation_method, $attach_function);

                            return $entity;
                        } 
                    );
                }
                else{
                    
                    $relation = $this->convert(
                        // relation data
                        $relation,
                        // relation's class
                        $relation_class,
                        // parser object
                        $parser
                    );

                    // attach relation to model
                    $this->attachRelated($model, $relation, $relation_method, $attach_function);
                    
                } // endif 
                
//dump( $relation );

                return $relation;
                
            });
        }
//ddd('after all', $entity);        
        return $model;
    } // end convert()

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
            // no relation methods
            return;
        }
        
        if(is_array($model->$relation_method())){
            // some models have multiple methods for a related model class (SharedData -> StatusEffect)
            
            collect($model->$relation_method())->each(function($method) use ($model, $relation, $attach_function) {
                $model->$method()->$attach_function($relation);
            });
            $model->save();
            
            return;         
        }
        
        $model->$relation_method()->$attach_function($relation);
        $model->save();
    }     
}