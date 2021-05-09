<?php
namespace App;

class JsonAdapter
{

    /**
     * decode a JSON file
     *
     * @param  [string] $filepath filepath of JSON file
     *
     * @return Object    Decoded JSON
     */
    public static function decodeJsonFile($filepath, bool $toArray=false)
    {
        return json_decode(file_get_contents($filepath), $toArray);
    }

    /**
     * convert class name from JSON (remove prefix)
     * e.g., Recipe_ArmorBronzeChest -> ArmorBronzeChest
     * Then this naem can be used to find the item
     *
     * @param  string $name
     *
     * @return string       the trimmed name
     */
    public static function internalName(string $name)
    {
        return (explode('_', $name)[1]) ?? $name;
    }

    /**
     * convert camel case name to english
     * e.g., ArmorBronzeChest -> Armor Bronze Chest
     *
     * @param  string $name
     *
     * @return string       the trimmed name
     */
    public static function camelToEnglish(string $name)
    {
        // split string into array on uppercase letter and turn it into string
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
    }

    public static function mapClassName($name, $namespace='App\Models\\')
    {
        // // dump("check class name: $name");
        if (class_exists($name)) {
            return $name;
        }

        $name = self::convertMemberName($name);
        $fqcname = $namespace.ucwords($name);

        // exact class name
        if (class_exists($fqcname)) {
            return $fqcname;
        }
        // convert from plural class name
        if (class_exists(rtrim($fqcname, 's'))) {
            return rtrim($fqcname, 's');
        }
        // is recipe object
        if (str_contains(strtolower($name), 'recipe')) {
            return $namespace.'Recipe';
        }
        // is Item
        if (str_contains(strtolower($name), 'resitem')) {
            return $namespace.'Item';
        }

        // is statusEffect
        if (str_contains(strtolower($name), 'statuseffect')) {
            return $namespace.'StatusEffect';
        }

        return null;
    }
}
