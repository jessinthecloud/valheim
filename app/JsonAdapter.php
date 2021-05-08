<?php
namespace App;

class JsonAdapter
{
    public static $count=0;
    private static $childPropNames = [
        'resources',
        'item',
        'craftingStation',
        'sharedData',
        'skillType',
        'itemType',
        'animationState',
        'statusAttribute',
        'attackStatusEffect',
        'consumeStatusEffect',
        'equipStatusEffect',
        'setStatusEffect',
    ];

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

    public static function removeIgnored($className, $data, $ignore)
    {
        // dump(" !! REMOVING IGNORED !!
        // ignore in $className: ", array_flip($ignore));
        // dump("keep: ", array_diff_key($data, array_flip($ignore)));
        return array_diff_key($data, array_flip($ignore));
    }

    public static function attachRelationsTo($object, $data)
    {
        // dump("checking for relations in data: ", $data, ' from set: ', self::$childPropNames);
        $vars = $object->getGuarded();
        // dump("CHECKING FOR ATTACHABLE VARS: ", $vars);

        foreach ($vars as $propName) {
            // dump("propname: $propName");
            if (in_array($propName, self::$childPropNames)) {
                dump("CHILD $propName FOUND");
                if (isset($data[$propName])) {
                    // dump($data[$propName]);
                    self::determineAttach($object, $data[$propName], $propName);
                } else {
                    dump("child is empty");
                }
            }
        } // end foreach

        return $object;
    }

    public static function determineAttach($object, $child, $propName)
    {
        // we can use "s" because only the plurals end in s here
        if (substr($propName, -1) === 's') {
            // many to many
            if (is_array($child)) {
                $children = [];
                foreach ($child as $c) {
                    // dump("child", $c);
                    if (is_object($c)) {
                        $children []= $c;
                        // dump("attaching child ", $c);
                        $object->$propName()->save($c);
                    // $object->$propName()->create($c);
                    // $object->$propName()->associate($c);
                        // $object->$propName()->attach($c);
                        /*$object->$propName = $c;
                        $object->save();*/
                    } else {
                        $children []= self::mapClassName($propName)::updateOrCreate($c);
                        // dump("attaching child ", end($children));
                        $object->$propName()->save(end($children));
                        // $object->$propName()->attach(end($children));
                        // $object->$propName()->create(end($children));
                        // $object->$propName()->associate(end($children));
                    }
                }
                dump("many to many -- $propName -- NOT DONE");
                // dump($children);
                // dump(array_column($children, 'id'));
                return; // $object->$propName()->attach(array_column($children, 'id'));
            }
            // dump("one to many");
            return $object->$propName()->save($child);
        }
        // one to ?
        dump("one to one $propName");
        if (!is_object($child)) {
            $child = self::mapClassName($propName)::updateOrCreate($child);
        }
        return $object->$propName()->associate($child);
    }
}
