<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JsonAdapter extends Model
{
    use HasFactory;
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
        dump((explode('_', $name)[1]) ?? $name);
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
        dump(trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name);
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
        dump("*~*~*~*~*~*~*~*~*~*~*");
        dump("CREATING $className");
        dump($data);
        $className = self::convertMemberName($className);
        $className = self::mapClassName($className);
        dump("real class name: $className");
        foreach ($data as $key => &$val) {
            $newkey = self::convertMemberName($key) ?? $key;
            // dump("KEY: $key -- real key: $newkey -> ");
            // dump($val);
            $data [$newkey]= $val;
            if ($key !== $newkey) {
                unset($data[$key]);
            }
            // dump($data[$newkey]);
            // dump("===");
        }
        dump($data);
        dump("*~*~*~*~*~*~*~*~*~*~*");

        return $className ? new $className($data) : null;
    }

    public static function recursiveCreateFromArray($data)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($data),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $i=0;
        foreach ($iterator as $key => $item) {
            if ($i > 44) {
                break;
            }
            if (is_array($item)) {
                // item is array
                if (is_int(array_key_first($item))) {
                    // item is numeric array
                    if ($i > 0) {
                        // if not first item, key is also property of CURRENT object
                        dump("Current object property name: $key");
                        dump("Child Object name: $key");
                        dump("Child Objects: ");
                        dump($item);
                    } else {
                        dump("Object name: $key");
                        dump("Objects: ");
                    }
                    // current key is object name, item is array of these objects
                    dump($item);
                } else {
                    // item is assoc array
                    if (is_int($key)) {
                        // PREVIOUS key was object name, item is ARRAY of that object's  properties => values
                        dump("-- Object name: PREVIOUS KEY");
                        dump("-- Properties->Values: ");
                        dump($item);
                    } else {
                        // key is property AND object name, item is ARRAY of properties => values
                        dump("Previous object Property name: $key");
                        dump("-- Object name: $key");
                        dump("-- Properties->Values: ");
                        dump($item);
                    }
                }
            } else {
                // item is NOT array
                // PREVIOUS key is object name
                dump("-- Object name: PREVIOUS KEY");
                // key is property name, item is property value
                dump("$key => $item");
            }
            $i++;
        } // end foreach
    }

    public static function createFromArray($data, $name='')
    {
        self::$count++;
        if (self::$count >= 20) {
            die;
        }
        $obj = null;
        if (is_array($data)) {
            dump("=====");
            dump('DATA IS ARRAY');
            dump($data);
            dump("=====");
            ///////////////////////////////////////////////////////
            foreach ($data as $key => $item) {
                if (!is_int($key)) {
                    dump('Key is NOT int: '.$key);
                    if (is_array($item)) {
                        // item is array
                        if (is_int(array_key_first($item))) {
                            dump("item is NUMERIC ARRAY");
                            dump("key is objectname, item is array of these objects");
                            dump("Obj Name: $key");
                            dump("Objects: ");
                            dump($item);
                            dump("UPPER (".self::$count.") CREATE FROM ARRAY");
                            $obj []= self::createFromArray($item, $key);
                        } else {
                            dump("item is ASSOC ARRAY");

                            dump("key is objectname, item is array of obj properties");
                            dump("Obj Name: $key");
                            dump("Properties: ");
                            dump("UPPER (".self::$count.") CREATE OBJECT");

                            dump($item);
                            // create object
                            return self::createObject($key, $item);
                        }
                    } else {
                        // item not array
                        dump("item is NOT array: $item");
                        dump("key is property name, item is property value");
                        dump("$key => $item");
                    }
                } else {
                    // key is int
                    dump('--- Key is INT: '.$key);
                    if (is_array($item)) {
                        dump("--- item is ARRAY");

                        dump("--- key is int ($key) item is array of obj properties");
                        dump("--- LOWER (".self::$count.") CREATE OBJECT");

                        return self::createObject($name, $item);
                    } else {
                        // item is not array
                        dump("--- item is NOT array: $item");
                        dump("--- key is objectname, item is obj property");
                        dump("--- Obj Name: $key");
                        dump("--- Property: $item");
                    }
                }
                dump("=/=/=/=/=/=/=");
            } // end foreach
        } else {
            dump("DATA is NOT Array: ".$data);
            dump("//////////////////////////////////////////////////////");
        }
        return $obj;
    }

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
                && $className !== get_class($parent)*/) {
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
                }*/
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
            dump($leaf);*/


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
             */
            $i++;
        }

        return $parent;

        /*if (property_exists($className, $keyname)) {
            // TODO: correctly fill val if it is array
            if (is_array($val)) {
                $object->$keyname []= self::createFromJson($val, 'App\Models\Valheim\\'.rtrim(ucwords($keyname), 's'));
            } else {
                $object->$keyname = $val;
            }
        }*/
        // return $object;
    }
}
