<?php

namespace App\Converters;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DataParser
{
    /**
     * create name info from data
     *
     * @param        $data
     * @param string $class
     *
     * @return array
     */
    public function parse($data, string $class) : array
    {
        
        $data = collect($data);

        // make sure $data is more than 1 dimensional before looping
        // otherwise, make $data an array and convert its names directly 
        // check all, not just first
        $flat_data = $data->filter(function($entity){
            return !is_array($entity);
        });

        $multi_data = $data->diffAssoc($flat_data);
        
        $multi_data = $multi_data->map(function($entity) use ($class) {
            // check all, not just first item
            return $this->convertNames( $entity, $class );
        });

        $flat_data = $this->convertNames( $flat_data->all(), $class );

        return $multi_data->merge($flat_data)->all();
    } // end parse()

    /**
     * populate name and slug data
     *
     * @param array  $info
     * @param string $class
     *
     * @return array [type]       [description]
     */
    protected function convertNames(array $info, string $class) : array
    {
        // some models have longer raw_names (i.e., raw_crafting_station_name)
        $raw_name_index = (array_key_exists('raw_name', $info)) ? 
            'raw_name' : $this->parseRawName($info, $class);
          
        // if strange case where only true name exists e.g., Recipe_Adze
        // or only prefab name exists (e.g., StoneGolem_clubs shared data)
        $info['raw_name'] = $info[$raw_name_index] ?? (isset($info['true_name']) ? $this->removeCsPrefix($info['true_name']) : ($info['prefab_name'] ?? ''));

        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        // true name as slug since it is unique (i.e., alt recipes like Bronze5, or fart -> block_attack_aoe)
        $info['slug'] = isset($info['true_name']) ? Str::slug(trim($this->prettify($info['true_name']))) : Str::slug(trim($info['name']));

        return $info;
    }

    /**
     * Determine raw name index if it's special
     * 
     * @param array  $info
     * @param string $class
     *
     * @return mixed
     */
    private function parseRawName(array $info, string $class)
    {
        $keys = array_keys($info);
        // get first part of class name
        $class_prefix = Str::snake(Str::afterLast($class, '\\'));

        return collect($keys)->filter(function($index_name) use($class_prefix) {

            $index_class = Str::lower(Str::between($index_name, 'raw_', '_'));

            return (Str::contains($index_name, ['raw', 'name']) 
                && Str::startsWith($index_class, $class_prefix));
        })->first();
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
    public static function prettify(string $name) : string
    {
        $name = Str::of(trim($name))->replace('_', ' ');
        $name = Str::of($name)->split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/')->toArray();
        
        return implode(' ', $name) ?? $name;
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
    public static function removeCsPrefix(string $name) : string
    {
        return Str::after($name, '_');
    }

    /**
     * Determine table name from model name
     * 
     * @param string $model_class
     *
     * @return string table name based on class
     */
    public function parseTable( string $model_class ) : string
    {
        return Str::snake(Str::pluralStudly(Str::afterLast($model_class, '\\')));
    }

    /**
     * Make sure slug is unique
     *
     * @param string $slug
     * @param string $class
     *
     * @return string
     */
    public function checkAndSetSlug(string $slug, string $class) : string
    {
        // check model has slug col
        if(Schema::hasColumn($this->parseTable($class), 'slug')) {
            // check if slug exists
            // needed where there is no true name, i.e. shared data for block_attack_aoe
            $slug_count = $class::where( 'slug', 'like', $slug . '%' )->count();
            if ( $slug_count > 0 ) {
                // append to create unique slug 
                $slug .= '-' . ( $slug_count + 1 );
            }
        }

        // for recipe only
        return Str::after($slug, 'recipe-');
    }
}