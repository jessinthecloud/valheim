<?php

namespace App\Converters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FileConverter
{
    // model data is converted for
    protected string $class;
    // default DB table for model
    protected string $class_table;
    // data to be converted
    protected $data;
    // the objects created during conversion
    public $models;

    // path to json files
    protected string $filepath;
    protected string $filename;
    // json filepath+name to convert
    protected string $file;

    // offset number 
    protected int $chunkOffset;
    // limit number
    protected int $chunkAmount = 50;
    // current set of values (array or collection?)
    protected $chunk;
    // contents of file
    protected string $contents;

    public function __construct( string $class )
    {
        $this->class = $class;
        $this->class_table = Str::snake(Str::pluralStudly(Str::afterLast($this->class, '\\')));
        $this->filepath = config('filesystems.json_path');
        $this->filename = Str::kebab(Str::afterLast($this->class, '\\')).'-list.json';
        // json file is kebab'd model name
        $this->file = $this->filepath.'\\'.$this->filename;
        $this->contents = file_get_contents($this->file) ?? '';
    }

    public function convert()
    {
        $this->prepareData();
        $this->create();
    }

    /**
     * simple relations only need a few lines of the same code
     *
     * @param        $data
     * @param        $model
     * @param string $relation_method
     * @param string $relation_class
     * @param string $relation_column
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
     * create name info from data
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
//dump('prepareData()');
//dump('unprepared data of '.$class.': ', $data);
        $decoded_data = $data ?? $this->data;
        $class = $class ?? $this->class;
        $table = isset($class) ? Str::snake(Str::pluralStudly(Str::afterLast($class, '\\'))) : $this->class_table;

        $decoded_data = collect($decoded_data)->unique();
//dump('decoded data, unique only: ', $decoded_data);
        // use passed in class to find table or use converter's known table

        // make sure $decoded_data is more than 1 dimensional before looping
        // otherwise, make $decoded_data an array and convert its names directly 
        $prepared_data = ( null !== $decoded_data->first() && !is_scalar($decoded_data->first()) )  ? $decoded_data->map(function($entity) use ($class, $table) {

//dump('entity stuff: ', $entity, 'class: '.$class, 'table: '.$table );

            return $this->convertNames( $entity, $class, $table );
        }) : $this->convertNames( $decoded_data->all(), $class, $table );

////dump($decoded_data);

        if(isset($data)){
            // using passed around data, not global to class
            return $prepared_data;
        }
//dump('++++ SET $THIS->DATA ++++');
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

    /**
     * populate name and slug data
     *
     * @param array       $info
     * @param string|null $class
     *
     * @return array [type]       [description]
     */
    protected function convertNames(array $info, string $class=null, string $table=null)
    {
//dump('convertNames()');
        // allow class to be passed in
        $class = $class ?? $this->class;

        // if strange case where only true name exists e.g., Recipe_Adze
        // or only prefab name exists (e.g., StoneGolem_clubs shared data)
        $info['raw_name'] = $info['raw_name'] ?? (isset($info['true_name']) ? $this->removeCsPrefix($info['true_name']) : ($info['prefab_name'] ?? ''));

        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        // true name as slug since it is unique (i.e., alt recipes like Bronze5, or fart -> block_attack_aoe)
        $info['slug'] = isset($info['true_name']) ? Str::slug(trim($this->prettify($info['true_name']))) : Str::slug(trim($info['name']));

        // check model has slug col
        if(Schema::hasColumn($table, 'slug')) {
            // check if slug exists
            // needed where there is no true name, i.e. shared data for block_attack_aoe
            $slug_count = $class::where( 'slug', 'like', $info['slug'] . '%' )->count();
            if ( $slug_count > 0 ) {
                // append to create unique slug 
                $info['slug'] = $info['slug'] . '-' . ( $slug_count + 1 );
                return $info;
            }
        }

        // for recipe only
        $info['item_slug'] = Str::after($info['slug'], 'recipe-');

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