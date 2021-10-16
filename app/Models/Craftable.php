<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Craftable extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

    abstract public function requirementFor();

    abstract public function type();
    
    // split string into array on uppercase letter and turn it into string
    public static function niceName( $name )
    {
        return trim( implode( ' ', preg_split( '/(?=[A-Z])/', $name ) ) ) ?? $name;
    }
}
