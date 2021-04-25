<?php
namespace App;

class JsonAdapter
{
    public static $count=0;
    private static $childPropNames = [
        'resources',
        'item',
        'crafting_station',
        'shared'
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
     * convert member name from C# (remove "m_")
     *
     * @param  string $name member name
     *
     * @return string       the trimmed name
     */
    public static function convertMemberName(string $name)
    {
        // dump("check member name: $name");
        $name = (str_contains($name, 'resItem')) ? 'item' : $name;
        // $name = (str_contains($name, 'shared')) ? 'shared_data' : $name;
        $name = (str_contains($name, 'craftingstation')) ? 'crafting_station' : $name;

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

    public static function mapClassName($name)
    {
        // dump("check class name: $name");
        if (class_exists($name)) {
            return $name;
        }

        $name = self::convertMemberName($name);
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
        if (str_contains($name, 'resItem')) {
            return 'App\Models\\'.'Item';
        }

        return null;
    }

    public static function createObject($className, $data)
    {
        dump("*~* CREATING $className (".self::mapClassName($className).")*~*");

        $className = self::mapClassName($className);
        // dump("fixed name: $className");
        if (is_array($data)) {
            // convert property names to correct names
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $keyClass = self::mapClassName($key);
                    // dump("key: $key, class: $keyClass");
                    if (class_exists($keyClass)) {
                        // dump("### EXISTS! create $keyClass with val ###");
                        $v = $val;
                        // dump($v);
                        if (is_int(array_key_first($val))) {
                            $val = self::createFromArray($val, $keyClass);
                        } else {
                            $val = self::createObject($keyClass, $val);
                        }
                        // dump("-- V:", $v);
                        $data[$key] = $val;
                        // dump("data[key]: ", $data[$key]);
                    }
                } /*else {
                    dump("key: $key");
                }*/
                // dump("val: ", $val);
                // save key as the nice name
                $newkey = self::convertMemberName($key) ?? $key;
                // dump("newkey: $newkey");
                if ($newkey !== 'itemData') {
                    $data [$newkey]= $val;
                }
                // dump("data[NEWkey]: ", $data[$newkey]);
                if ($key !== $newkey) {
                    unset($data[$key]);
                }
                // dump("=-=-=-=-=-=-=");
            } // endforeach
        } // end if is array
        if (isset($className)) {
            dump(" @@@ $className being created! @@@");
            /*if (isset($data['name'])) {
                dump("name: {$data['name']}");
            }*/
            dump($data);
            $object = !is_object($data) ? $className::create($data) : $data;
            dump($object);
            self::attachRelationsTo($object, $data);
            /*if (isset($object->name) && $className::where('name', $object->name)->first()) {
                dump("{$object->name} already exists. ({$object->id})");
            } else {
                // dump("object doesn't exist or DOESN'T HAVE A NAME");
                // check and attach relations
                // need to find
                // dump("before $className save: ", $object);
                $saved = $object->save();
                // dump("save $className: ".$saved);
                $object = JsonAdapter::attachRelationsTo($object);
            }*/
            return $object;
        } // if classname set
        return null;
    }

    public static function attachRelationsTo($object, $data)
    {
        // dump("checking for relations");
        $vars = $object->getGuarded();
        // dump("CHECKING FOR ATTACHABLE VARS: ", $vars);

        foreach ($vars as $propName) {
            dump("propname: $propName");
            if (in_array(strtolower($propName), self::$childPropNames)) {
                dump("CHILD FOUND");
                // TODO: query for this child and save to $object
                if (isset($data[$propName])) {
                    dump($data[$propName]);
                    self::determineAttach($object, $data[$propName], $propName);
                }
                // dump("attacher: $attacher");
                // $object->$attacher()->save($value);
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
                    dump("child", $c);
                    if (is_object($c)) {
                        $children []= $c;
                        dump("attaching child ", $c);
                        $object->$propName()->save($c);
                    // $object->$propName()->create($c);
                    // $object->$propName()->associate($c);
                        // $object->$propName()->attach($c);
                        /*$object->$propName = $c;
                        $object->save();*/
                    } else {
                        $children []= self::mapClassName($propName)::create($c);
                        dump("attaching child ", end($children));
                        $object->$propName()->save(end($children));
                        // $object->$propName()->attach(end($children));
                        // $object->$propName()->create(end($children));
                        // $object->$propName()->associate(end($children));
                    }
                }
                dump("many to many -- $propName");
                // dump($children);
                // dump(array_column($children, 'id'));
                return; // $object->$propName()->attach(array_column($children, 'id'));
            }
            dump("one to many");
            return $object->$propName()->save($child);
        }
        // one to ?
        dump("one to one $propName");
        if (!is_object($child)) {
            $child = self::mapClassName($propName)::create($child);
        }
        return $object->$propName()->associate($child);
    }

    public static function createFromArray($data, $name='', $objarray=[])
    {
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                self::$count++;
                /*if (self::$count > 10) {
                    dump("DYING");
                    die;
                }*/
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

    public static function convertFromModel($model)
    {
        $type = $model->getTable();
        dump($type);
        dump("========");
        $contents = self::decodeJsonFile(
            storage_path('app\\'.strtolower($type).'.json'),
            true
        );
        $name = $model->name;
        $found = false;
        foreach ($contents as $i => $content) {
            if ($i >= 10) {
                break;
            }
            foreach ($content as $key => $value) {
                dump("$value === $name");
                dump($value === $name);
                if ($value === $name) {
                    $found = true;
                    break;
                }
            };

            if ($found) {
                $contents = $content;
                break;
            }
        }
        dump($contents);
        die;
    }

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
