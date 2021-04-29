<?php
namespace App;

use App\Enums\StatusAttribute;
use App\Enums\AnimationState;
use App\Enums\SkillType;
use App\Enums\ItemType;

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
     * convert member name from C# (remove "m_")
     *
     * @param  string $name member name
     *
     * @return string       the trimmed name
     */
    public static function convertMemberName(string $name)
    {
        // // dump("check member name: $name");
        $name = (str_contains($name, 'resItem')) ? 'item' : $name;
        $name = (str_contains($name, 'shared')) ? 'sharedData' : $name;
        $name = ($name === 'm_name') ? 'interpolated_name' : $name;

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

    public static function createObject($className, $data)
    {
        // dump("*~* CREATING $className (".self::mapClassName($className).")*~*");
        // dump($data);
        $className = self::mapClassName($className);
        // dump("fixed name: $className");
        if (!empty($className::$ignore) && !empty($data)) {
            // remove ignored properties
            $data = self::removeIgnored($className, $data, $className::$ignore);
        }
        if (is_array($data)) {
            if (array_key_exists('m_itemData', $data) && !empty($data['m_itemData'])) {
                // flatten itemData for items
                $data = array_merge($data, $data['m_itemData']);
                // remove old itemData
                unset($data['m_itemData']);
                // dump("ITEMDATA MERGED: ", $data);
            }

            // convert property names to correct names
            foreach ($data as $key => $val) {
                // look for Model
                $keyClass = self::mapClassName($key);
                // dump("key: $key, class: $keyClass");
                // find the class for this property
                if (class_exists($keyClass)) {
                    // dump("### EXISTS! create $keyClass with val ###");
                    if (is_array($val) && is_int(array_key_first($val))) {
                        $val = self::createFromArray($val, $keyClass);
                    } else {
                        $val = self::createObject($keyClass, $val);
                    }
                    // $data[$key] = $val;
                    // dump("data[key]: $key", $data[$key]);
                    // dump("val: ", $val);
                } else {
                    // look for enums
                    $enumName = self::mapClassName($key, 'App\Enums\\');
                    if (class_exists($enumName)) {
                        $enumVar = strtoupper($val);
                        // dump("### ENUM FOUND $enumName :: $enumVar ### ".constant("$enumName::$enumVar"));
                        // use constant() to fix 'undeclared static constant' error
                        $val = constant("$enumName::$enumVar");
                        // dump("data[key]: $key", $data[$key]);
                    }
                }

                // save key as the nice name but don't overwrite another existing key
                if (self::convertMemberName($key) !== null
                    && !in_array(self::convertMemberName($key), array_keys($data))) {
                    $newkey = self::convertMemberName($key);
                } else {
                    $newkey = $key;
                }

                // // dump("newkey: $newkey");
                // if ($newkey !== 'itemData') {
                $data [$newkey]= $val;
                // }
                // dump("data[NEWkey]: $newkey", $data[$newkey]);
                // dump("val: ", $val);

                if ($key !== $newkey) {
                    unset($data[$key]);
                }
                // // dump("=-=-=-=-=-=-=");
            } // endforeach
        } // end if is array
        // dump("-----------------------------
// NEW DATA: ", $data, "
// ------------------------------------");
        if (isset($className) && !empty($data)) {
            $msg = " @@@ $className is being initialized! @@@";
            if (isset($data['name'])) {
                $msg .= " -- name: {$data['name']}";
            }
            $msg .= " -- with data: ";
            dump($msg);
            // make data for table (props that match columns)
            if (!is_object($data) && !empty($className::$ignoreInTable)) {
                // remove properties that aren't directly DB columns
                $dataForTable = self::removeIgnored($className, $data, $className::$ignoreInTable);
            // dump("-- FOR TABLE: ", $dataForTable);
            } else {
                $dataForTable = $data ?? [];
            }
            // dump($dataForTable);

            $object = !is_object($dataForTable) ? $className::updateOrCreate($dataForTable) : $dataForTable;

            // dump("BASE OBJ CREATED: ", $object);
            self::attachRelationsTo($object, $data);
            $object->save();
            dump("$$$ $className FINISHED $$$");
            return $object;
        } // if classname set
        return null;
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
                            // dump('+++ INSIDE ::: is array of OBJECTS +++
                            // createFromArray($item, $key, $objarray)');
                            // dump($item, 'key: '.$key, $objarray);
                            return self::createFromArray($item, $key, $objarray);
                        } else {
                            // item is ASSOC ARRAY");

                            // key is objectname, item is array of obj properties
                            // dump('+++ INSIDE ::: is ASSOC array+++
                            // createObject($key, $item)');
                            // dump('key: '.$key, $item);
                            // create object
                            return self::createObject($key, $item);
                        }
                    } else {
                        // item is not array
                        // key is property name, item is property value
                        // create obj from all data this loop
                        // should probably check each property for class names instead
                        // dump('+++ INSIDE ::: is NOT array +++
                        // createObject($key, $item)');
                        // dump('key: '.$key, $item);
                        return self::createObject($name, $data);
                    }
                } else {
                    // key is int

                    if (is_array($item)) {
                        // item is array
                        // dump('+++ INSIDE ::: is array +++
                        // createFromArray($item, $key, $objarray)');
                        // dump($item, 'key: '.$key, $objarray);
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
        // dump($type);
        // dump("========");
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
                // dump("$value === $name");
                // dump($value === $name);
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
//         dump($contents);
        die;
    }
}
