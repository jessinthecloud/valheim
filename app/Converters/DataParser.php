<?php

namespace App\Converters;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DataParser
{
    /**
     * create name info from data
     *
     */
    public function parse($data)
    {
//dump('parse()');
        
        $data = collect($data)->unique();

        // make sure $data is more than 1 dimensional before looping
        // otherwise, make $data an array and convert its names directly 
        $data = ( null !== $data->first() && !is_scalar($data->first()) )  ? $data->map(function($entity) {
            
            return $this->convertNames( $entity );

        })->all() : $this->convertNames( $data->all() );
//dump('converted names for: ', $data );   

        return $data;
    } // end parse()

    /**
     * populate name and slug data
     *
     * @param array       $info
     *
     * @return array [type]       [description]
     */
    protected function convertNames(array $info) : array
    {
//dump('convertNames()', $info);

        // if strange case where only true name exists e.g., Recipe_Adze
        // or only prefab name exists (e.g., StoneGolem_clubs shared data)
        $info['raw_name'] = $info['raw_name'] ?? (isset($info['true_name']) ? $this->removeCsPrefix($info['true_name']) : ($info['prefab_name'] ?? ''));

        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        // true name as slug since it is unique (i.e., alt recipes like Bronze5, or fart -> block_attack_aoe)
        $info['slug'] = isset($info['true_name']) ? Str::slug(trim($this->prettify($info['true_name']))) : Str::slug(trim($info['name']));

        return $info;
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
        if(Schema::hasColumn($class, 'slug')) {
            // check if slug exists
            // needed where there is no true name, i.e. shared data for block_attack_aoe
            $slug_count = $class::where( 'slug', 'like', $slug . '%' )->count();
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