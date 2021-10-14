<?php

namespace App\Converters;

use Illuminate\Support\Str;

class DataParser
{
    // data parsed
    protected $data;

    public function __construct()
    {
    }

    /**
     * create name info from data
     *
     */
    public function parse($data)
    {
//dump('parse()');
        $this->data = $data;
        
        $this->data = collect($this->data)->unique();

        // make sure $this->data is more than 1 dimensional before looping
        // otherwise, make $this->data an array and convert its names directly 
        $this->data = ( null !== $this->data->first() && !is_scalar($this->data->first()) )  ? $this->data->map(function($entity) {


            return $this->convertNames( $entity );

        }) : $this->convertNames( $this->data->all() );
//ddd('convert names for: ', $this->data );   

        return $this->data;
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
//dump('convertNames()');

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
}