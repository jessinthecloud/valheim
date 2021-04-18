<?php

namespace App\Models;

use App\Models\ItemSharedData;
use App\Http\Controllers\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*public string $name; // internalID - as seen on wiki
    public string $name_EN; // internalID - as seen on wiki
    // public $data; // ItemData -- not used, is not instanced from game
    public $shared_data; // ItemSharedData*/

    public function __construct($data=null)
    {
        $this->name = $data['name'] ?? null;
        $this->name = $data['name_EN'] ?? JsonAdapter::camelToEnglish($this->name);
        // $this->data = $data['itemData'] ?? null;
        $this->shared_data = $data['shared'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
    public function setName_ENAttribute($value)
    {
        $this->attributes['name_EN'] = $value;
    }
    public function setShared_DataAttribute($value)
    {
        $this->attributes['shared_data'] = $value;
    }

    public function sharedData()
    {
        return $this->hasOne(ItemSharedData::class);
    }
}
