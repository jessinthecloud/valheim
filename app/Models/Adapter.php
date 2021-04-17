<?php

namespace App\Models;

use App\Models\Recipe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Adapter extends Model
{
    use HasFactory;

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

    protected static function mapClassName($name)
    {
        $fqcname = __NAMESPACE__.'\\'.ucwords($name);
        // exact class name
        if (class_exists($fqcname)) {
            return $fqcname;
        }
        // convert from plural class name
        if (class_exists(rtrim($fqcname, 's'))) {
            return rtrim($fqcname, 's');
        }
        // is recipe object
        if (strpos($name, 'Recipe') !== false) {
            return __NAMESPACE__.'\\'.'Recipe';
        }
        // is Item
        if (strpos($name, 'Item') !== false) {
            return __NAMESPACE__.'\\'.'Item';
        }

        return null;
    }

    public static function createFromJson($data)
    {
        // associative arrays are objects
        // numeric arrays are arrays

        dump($data);

        echo "//////////////////////////////////////////////////////";

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($data),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $i=0;
        foreach ($iterator as $key => $leaf) {

            // remove m_ prefix if exists
            if (!is_int($key)) {
                $key = self::convertMemberName($key);
                $className = self::mapClassName($key);
                if (strpos(strtolower($key), 'name') !== false) {
                    // the key is the class name
                    $className = self::mapClassName($leaf);
                }
                if (isset($className) && class_exists($className)) {
                    // we found a class, create object
                    $object = new $className();
                    dump("OBJECT: $className");
                } else {
                    // non-class key
                    dump("$key => $leaf");
                    // dump($leaf);
                }
            }


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
