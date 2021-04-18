<?php
namespace App;

class JsonAdapter
{
    public static $count=0;

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
     * convert member name from C# (remove "m_")
     *
     * @param  string $name member name
     *
     * @return string       the trimmed name
     */
    protected static function convertMemberName(string $name)
    {
        return (substr($name, 0, 2) === 'm_') ? substr($name, 2) : $name;
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
    protected static function internalName(string $name)
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
    protected static function camelToEnglish(string $name)
    {
        // split string into array on uppercase letter and turn it into string
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
    }

    protected static function mapClassName($name)
    {
        $fqcname = 'App\Models\\'.ucwords($name);

        // exact class name
        if (class_exists($fqcname)) {
            return $fqcname;
        }
        // convert from plural class name
        if (class_exists(rtrim($fqcname, 's'))) {
            return rtrim($fqcname, 's');
        }
        // is recipe object
        if (str_contains($name, 'Recipe')) {
            return 'App\Models\\'.'Recipe';
        }
        // is Item
        if (str_contains($name, 'Item')) {
            return 'App\Models\\'.'Item';
        }

        return null;
    }

    public static function createObject($className, $data)
    {
        $className = self::convertMemberName($className);
        $className = self::mapClassName($className);

        if (is_array($data)) {
            foreach ($data as $key => &$val) {
                $newkey = self::convertMemberName($key) ?? $key;
                $data [$newkey]= $val;
                if ($key !== $newkey) {
                    unset($data[$key]);
                }
            }
        }

        return $className ? new $className($data) : null;
    }



    public static function createFromArray($data, $name='', $objarray=[])
    {
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                if (!is_int($key)) {
                    // key is not int
                    if (is_array($item)) {
                        // item is array
                        if (is_int(array_key_first($item))) {
                            // key is objectname, item is array of these objects");

                            return self::createFromArray($item, $key, $objarray);
                        } else {
                            // item is ASSOC ARRAY");

                            // key is objectname, item is array of obj properties

                            // create object
                            return self::createObject($key, $item);
                        }
                    } else {
                        // item is not array
                        // key is property name, item is property value
                        // create obj from all data this loop
                        // should probably check each property for class names instead
                        return self::createObject($name, $data);
                    }
                } else {
                    // key is int

                    if (is_array($item)) {
                        // item is array

                        // item is array of obj properties

                        $objarray []= self::createFromArray($item, $name, $objarray);
                    } else {
                        // item is not array
                        // key is objectname, item is obj property
                    }
                }
            } // end foreach
        } else {
            // data is not an array
        }

        return $objarray ?? null;
    } // end createFromArray
    /*
    ///////////////////////
    public static function createFromJson($data, object $parent=null)
    {
        // associative arrays are objects
        // numeric arrays are arrays

        dump($data);

        dump("//////////////////////////////////////////////////////");

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($data),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $i=0;

        $className = $parent ? get_class($parent) : get_called_class();
        $parent = $parent ?? new $className();
        dump("PARENT: $className");
        dump("//////////////////////////////////////////////////////");

        foreach ($iterator as $key => $leaf) {

            // remove m_ prefix if exists
            if (!is_int($key)) {
                $key = self::convertMemberName($key);
                if ($i > 0) {
                    $className = self::mapClassName($key) ?? $className;
                }
            }

            if (isset($className) && class_exists($className)/*
                && $className !== get_class($parent)*) {
                if (is_int($key)) {
                    dump('END OF CHILD LEAF '.$className);
                    dump(get_class($parent)."->$key = $className");
                    dump($leaf);
                    return self::createFromJson($leaf, new $className());
                } elseif ($leaf !== null) {
                    if (is_array($leaf)) {
                        // will be array of this child class
                        // dd(array_shift($leaf));
                        if (is_int(array_key_first($leaf))) {
                            dump("Array of CHILD: $className");
                            dump(get_class($parent)."->$key []= $className");
                            $parent->$key []= self::createFromJson(array_shift($leaf), new $className());
                        } else {
                            dump("END OF LEAF");
                            dump(get_class($parent)."->$key = $className");
                            $parent->$key = self::createFromJson($leaf, new $className());
                        }
                    } elseif ($className !== get_class($parent)) {
                        dump(get_class($parent)."->$key = $className");
                        $parent->$key = new $className();
                    }
                } /*else {
                    dump(get_class($parent)."->$key = $leaf");
                    $parent->$key = $leaf;
                }*
            } elseif (!is_int($key)) {
                echo "NO CLASS FOUND for $key";
                dump(get_class($parent)."->$key = $leaf");
                $parent->$key = $leaf;
            } else {
                // key is int
            }

            if (!is_int($key) && !is_array($leaf)) {
                // non-class key
                dump(get_class($parent)."->$key = $leaf");
                $parent->$key = $leaf;
            }
            /*dump('Key: '.$key."
Leaf: ");
            dump($leaf);*


            echo "=======================";

            /* if (is_array($leaf) && !is_int($key) && !is_int(array_key_first($leaf))) {
                 // this is an object (assoc array) key is obj name
                 echo "<BR>$key Is an obj<BR>";
                 // dump($key);
                 dump($leaf);
                 echo "====";
             } elseif (is_array($leaf) && !is_int($key)) {
                 // this is an array of objects (assoc array) key is obj name
                 echo "<BR>Array of $key obj<BR>";
                 // dump($key);
                 dump($leaf);
                 echo "====";
                 return self::createFromJson($leaf);
             } elseif (!is_array($leaf)) {
                 // dump($key);
                 dump($leaf);
                 echo "====";
             }
             *
            $i++;
        }

        return $parent;

    } // */
}
