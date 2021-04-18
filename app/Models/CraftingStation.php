<?php

namespace App\Models;

use App\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftingStation extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*public string $name;
    public string $name_EN;*/

    public function __construct($data=null/*$name=null, $name_EN=null*/)
    {
        $this->name = $data['name'] ?? '';
        $this->name_EN = $data['name_EN'] ?? $this->name;
    }

    /*public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
    public function setName_ENAttribute($value)
    {
        $this->attributes['name_EN'] = $value;
    }*/
}
