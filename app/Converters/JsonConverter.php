<?php

namespace App\Converters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

abstract class JsonConverter implements Converter
{
    // model data is converted for
    protected string $class;
    // default DB table for model
    protected string $class_table;
    // path to json files
    protected string $filepath;
    // json file to convert
    protected string $file;
    // data to be converted
    protected $data;
    // the objects created during conversion
    public $models;
    
    // offset number 
    protected int $chunkOffset;
    // limit number
    protected int $chunkAmount = 50;
    // current set of values (array or collection?)
    protected $chunk;

    public function __construct(string $class)
    {
        $this->class = $class;
        $this->class_table = Str::snake(Str::pluralStudly(Str::afterLast($this->class, '\\')));

        $this->filepath = config('filesystems.json_path');
        // json file is kebab'd model name
        $this->file = $this->filepath.'\\'.Str::kebab(Str::afterLast($this->class, '\\')).'-list.json';

    }

    /**
     * @required by Converter Interface 
     *          
     * Decode JSON 
     * Loop data
     * Insert into tables
     * Attach relationships
     */
    public function convert()
    {
        $this->data = $this->decode($this->file);
        $this->prepareData();
        $this->create();
    }

    /**
     * Attach relationship data to the inserted entity
     * (is just sharedData for Item)
     *
     * @param $data
     * @param $model
     */
    abstract protected function attachDataToModel($data, $model);

    /**
     * simple relations only need a few lines of the same code 
     * 
     * @param        $data
     * @param        $model
     * @param string $relation_method
     * @param string $relation_class
     * @param string $relation_column
     */
    protected function attachSingleRelation($data, $model, string $relation_method, string $relation_class, string $relation_column)
    {
        $related_model = $relation_class::where($relation_column, $data)->first();
        $model->$relation_method()->associate($related_model);
        $model->save();
    }

    /**
     * sanitize and decode file contents
     * allow for passing in data and returning as well as
     * using $this->data
     *
     * @param array|null  $data
     * @param string|null $class
     *
     * @return \Illuminate\Support\Collection|void
     */
    protected function prepareData(array $data=null, string $class=null)
    {
        $decoded_data = $data ?? $this->data;
        $decoded_data = collect($decoded_data)->unique();
        $table = isset($class) ? Str::snake(Str::pluralStudly(Str::afterLast($class, '\\'))) : $this->class_table;
if($class === 'App\Models\StatusEffect'){
dump($decoded_data);
}
        // only convert names if they exist
        // also make sure $decoded_data is more than 1 dimensional before looping
        $prepared_data = ( !is_string($decoded_data->first()) )  ? $decoded_data->map(function($entity) use ($class, $table) {
if($class === 'App\Models\StatusEffect'){
    dump($entity, $class, 'table: '.$table );
}
            return Schema::hasColumn($table, 'slug') ? $this->convertNames( $entity, $class ) : $entity;
        }) : ( Schema::hasColumn($table, 'slug') ? $this->convertNames( $decoded_data->all(), $class ) : $decoded_data);
        
        if(isset($data)){
            // using passed around data, not global to class
            return $prepared_data;
        }
        
        $this->data = $prepared_data;
    }

    /**
     * Loop data, convert names, insert into table
     * Allow for passing in data and returning as well as
     * using class data
     *
     * @return mixed
     */
    public function create(Collection $data=null, string $class=null)
    {
        $prepared_data = $data ?? $this->data;
        $model_class = $class ?? $this->class;
        $table = Str::snake(Str::pluralStudly(Str::afterLast($model_class, '\\')));

        // insert into table
        // make sure $prepared_data is 2+ dimensional
        $created_data = ( !is_string($prepared_data->first()) ) ? $prepared_data->map(function($entity) use ($model_class, $table) {
        
if($model_class === 'App\Models\StatusEffect'){
    dump('in created data map: ', $entity);
}
            $this->insertPreparedData($entity, $table, $model_class);
        }) : $this->insertPreparedData($prepared_data->all(), $table, $model_class);;
        
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
        // only try to insert columns that exist
        $db_values = Arr::only($entity, Schema::getColumnListing($table));

        $model = $model_class::firstOrCreate(
            $db_values
        );

        if(defined($model_class.'::RELATION_INDICES')) {
            // from the leftovers, get any that are also relationships that need to be mapped
            // use intersect to compare by keys and avoid issue with PHP trying to compare multidimensional values
            $relations = array_intersect_key( $entity, $model_class::RELATION_INDICES );
            $this->attachDataToModel($relations, $model);
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
        $related_data = $this->prepareData($related_data, $related_class);
if($related_class === 'App\Models\StatusEffect'){
    dump('prepared related data:', $related_data);
}       
        // make sure $related_data is Collection
        if( !is_a($related_data, Collection::class)){
            $related_data = collect($related_data);
        }
        return $this->create($related_data, $related_class);
    }

    /**
     * Remove invalid hex characters from a string
     *
     * @param  string $string string to sanitize
     *
     * @return string         sanitized string
     */
    public function removeInvalidHex(string $string)
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }

    /**
     * JSON decode
     * 
     * @param string $contents
     * @param bool   $associative
     *
     * @return mixed
     */
    public function decode(string $contents, $associative=true)
    {
        return json_decode(
            $this->removeInvalidHex(file_get_contents($contents)), 
            $associative
        );
    }

    /**
     * populate name and slug data
     *
     * @param array       $info
     * @param string|null $class
     *
     * @return array [type]       [description]
     */
    protected function convertNames(array $info, string $class=null)
    {
        // allow class to be passed in
        $class = $class ?? $this->class;

        // if strange case where only true name exists e.g., Recipe_Adze
        // or only prefab name exists (e.g., StoneGolem_clubs shared data)
        $info['raw_name'] = $info['raw_name'] ?? (isset($info['true_name']) ? $this->removeCsPrefix($info['true_name']) : $info['prefab_name']);
        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        // true name as slug since it is unique (i.e., alt recipes like Bronze5, or fart -> block_attack_aoe)
        $info['slug'] = isset($info['true_name']) ? Str::slug(trim($info['true_name'])) : Str::slug(trim($info['name']));
        // for recipe only
        $info['item_slug'] = Str::slug(trim($info['name']));
       
        // check if slug exists
        // needed where there is no true name, i.e. shared data for block_attack_aoe
        $slug_count = $class::where('slug', 'like', $info['slug'].'%')->count();
        if($slug_count > 0){
            // append to create unique slug 
            $info['slug'] = $info['slug'].'-'.($slug_count+1);
            return $info;
        }
        
        return $info;
    }

    /**
     * convert class name from C# (remove prefix)
     * e.g., Recipe_ArmorBronzeChest -> ArmorBronzeChest
     * Then this name can be used to find the item
     *
     * @param  string $name
     *
     * @return string       the trimmed name
     */
    public static function removeCsPrefix(string $name)
    {
        return Str::after($name, '_');
    }

    /**
     * Convert a name to pretty name by replacing underscores
     * with spaces, then splitting string into array on
     * camel or Studly case and turning it into a space delimited string
     * e.g., Recipe_ArmorBronzeChest -> Recipe Armor Bronze Chest
     *
     * regex: https://stackoverflow.com/questions/7593969/regex-to-split-camelcase-or-titlecase-advanced/7599674#7599674
     *
     * @param  string $name
     *
     * @return string       the converted name
     */
    public static function prettify(string $name)
    {
        $name = Str::of(trim($name))->replace('_', ' ');
        $name = Str::of($name)->split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/')->toArray();
        return implode(' ', $name) ?? $name;
    }
}