<?php

namespace App\Converters;

use Illuminate\Support\Str;

abstract class JsonConverter implements Converter
{
    // model data is converted for
    protected string $class;
    // path to json files
    protected string $filepath;
    // json file to convert
    protected string $file;
    // data to be converted
    protected $data;
    
    // offset number 
    private int $chunkOffset;
    // limit number
    private int $chunkAmount = 50;
    // current set of values (array or collection?)
    private $chunk;
    
    public function __construct(string $class)
    {
        $this->class = $class;

        $this->filepath = config('filesystems.json_path');
        // json file is kebab'd model name
        $this->file = $this->filepath.'\\'.Str::kebab(Str::afterLast($this->class, '\\')).'-list.json';

    }

    /**
     * Loop data, convert names, insert into table
     * 
     * @return mixed
     */
    abstract public function create();

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
        $this->prepareData();
        $this->create();

        dump($this->data);

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
        return json_decode($contents, $associative);
    }

    /**
     * sanitize and decode file contents
     */
    protected function prepareData()
    {
        $this->data = $this->decode(
            $this->removeInvalidHex(file_get_contents($this->file)
        ), true);
        
        $this->data = collect($this->data)->unique();
    }

    /**
     * populate name and slug data
     *
     * @param  array  $info
     *
     * @return [type]       [description]
     */
    protected function convertNames(array $info)
    {
        // TODO: look at this for refactoring

        // if strange case where only true name exists, or
        // e.g., Recipe_Adze
        if (!empty($info['true_name'])) {
            if (empty($info['raw_name'])) {
                $info['raw_name'] = $this->removeCsPrefix($info['true_name']);
            } elseif (str_contains(strtolower($info['true_name']), 'recipe') && preg_match('/[0-9]/', $info['true_name'])) {
                // if it is a recipe alt, use true name as unique slug but keep name to match to item created
                // e.g., Bronze5
                // add spaces to make pretty
                $info['name'] = $this->prettify(trim($info['raw_name']));
                $info['slug'] = Str::slug(trim($this->removeCsPrefix($info['true_name'])));
                $info['item_slug'] = Str::slug(trim($info['name']));
                $info['raw_slug'] = Str::slug(trim($info['raw_name']));
                $info['true_slug'] = isset($info['true_name']) ? Str::slug(trim($info['true_name'])) : null;


                return $info;
            }
        }

        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        $info['slug'] = Str::slug(trim($info['name']));
        $info['raw_slug'] = Str::slug(trim($info['raw_name']));
        $info['true_slug'] = isset($info['true_name']) ? Str::slug(trim($info['true_name'])) : null;

        if (!empty($info['true_name']) && str_contains(strtolower($info['true_name']), 'recipe')) {
            $info['item_slug'] = Str::slug(trim($info['name']));
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
        return (explode('_', $name)[1]) ?? $name;
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