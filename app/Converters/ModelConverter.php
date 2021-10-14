<?php

namespace App\Converters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelConverter implements Converter
{

    // model data is converted for
    public string $class;
    // default DB table for model
    public ?string $table;
    // data to convert
    protected $data;
    
    public function __construct(string $class, string $table=null)
    {
        $this->class = $class;
        $this->table = $table;
    }

    public function convert()
    {
        // TODO: Implement convert() method.
    }

    /**
     * @param mixed $data
     */
    public function setData( $data ) : void
    {
        $this->data = $data;
    }

    /**
     * simple relations only need a few lines of the same code
     *
     * @param        $value
     * @param        $model
     * @param string $relation_method
     * @param string $relation_class
     * @param string $relation_column
     * @param        $data
     */
    protected function attachSingleRelation($value, $model, string $relation_method, string $relation_class, string $relation_column, $data)
    {
//dump('attach', $relation_class, 'to', $model, 'via '.$relation_method.'() where '.$relation_column.' == '.$value);
        // find existing relation model
        // if not found, init conversion (creation) of new from the relation data
        $related_model = $relation_class::where($relation_column, $value)->first() ?? $this->convertRelated($data, $relation_class)->first();

//dump('related model found: ', $related_model);

        $model->$relation_method()->associate($related_model);
        $model->save();
    }

    /**
     * Loop data, convert names, insert into table
     *
     * @return mixed
     */
    public function create()
    {
//dump('create()');
        $prepared_data = $data ?? $this->data;
        $model_class = $class ?? $this->class;
        $table = Str::snake(Str::pluralStudly(Str::afterLast($model_class, '\\')));

        // insert into table
        // make sure $prepared_data is 2+ dimensional
        $created_data = ( null !== $prepared_data->first() && !is_scalar($prepared_data->first()) ) ? $prepared_data->map(function($entity) use ($model_class, $table) {
            $this->insertPreparedData($entity, $table, $model_class);
        }) : $this->insertPreparedData($prepared_data->all(), $table, $model_class);
//dump(' CREATED DATA: ', $created_data);
        if(isset($data)){
            // using passed around data, not global to class
            return $created_data;
        }

        $this->data = $created_data;
    }

    /**
     * @param array  $entity
     * @param string $table
     * @param string $model_class
     *
     * @return mixed
     */
    protected function insertPreparedData(array $entity, string $table, string $model_class)
    {
//dump('insertPrepared()');
        // only try to insert columns that exist
        $db_values = Arr::only($entity, Schema::getColumnListing($table));

        $model = $model_class::firstOrCreate(
            $db_values
        );

        if(defined($model_class.'::RELATION_INDICES')) {
            // from the leftovers, get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $model_class::RELATION_INDICES );

            // TODO: check if relation is already attached?

            if(!empty($relations)){
//dump('relations to attach: ', $relations);
                $this->attachDataToModel($relations, $model);
            }
            else{
//dump('-- no relations to attach --');
            }
        }

        return $model;
    }

    /**
     * convert data that doesn't exist and
     * needs to be attached via relationship
     *
     * @param array  $related_data
     * @param string $related_class
     *
     * @return mixed
     */
    protected function  convertRelated(array $related_data, string $related_class)
    {
//dump('convertRelated() -- '.$related_class);
        $related_data = $this->prepareData($related_data, $related_class);

        // make sure $related_data is Collection
        if( !is_a($related_data, Collection::class)){
            $related_data = collect($related_data);
        }
        return $this->create($related_data, $related_class);
    }
    
    protected function checkAndSetSlug(string $slug) : string
    {
        // check model has slug col
        if(Schema::hasColumn($this->class, 'slug')) {
            // check if slug exists
            // needed where there is no true name, i.e. shared data for block_attack_aoe
            $slug_count = $this->class::where( 'slug', 'like', $slug . '%' )->count();
            if ( $slug_count > 0 ) {
                // append to create unique slug 
                $slug .= '-' . ( $slug_count + 1 );
                return $slug;
            }
        }

        // for recipe only
        return Str::after($slug, 'recipe-');
    }
}