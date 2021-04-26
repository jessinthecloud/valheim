<?php
namespace App\Enums;

abstract class Enum
{
    /**
     * Allow returning enum name by value
     *
     * @param  [mixed] $val value of the enum
     *
     * @return string      name of the enum
     */
    public static function toString($val) : string
    {
        $enum = (new ReflectionClass(__CLASS__))->getConstants();
        $enum = array_flip($enum);
        if (!isset($enum[$val])) {
            throw new ErrorException("Enum does not exist");
        }
        return strtoupper($enum[$val]) ?? null;
    }
}
