<?php

namespace App\Models;

use App\Models\SharedData;
use App\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = ['itemData', 'sharedData'];
    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [
    ];
    /*
        vars to ignore on updateOrCreate()
     */
    public static $ignoreInTable = [
        'itemData',
        'sharedData'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'quality' => 1,
        'variant' => 0,
        'durability' => 100
    ];

    /*public string $name; // internalID - as seen on wiki
    public string $name_EN; // internalID - as seen on wiki
    // public $data; // ItemData -- not used, is not instanced from game
    */
    // public $shared_data; // ItemSharedData

    /*public function __construct($data=null)
    {
        $this->name = $data['name'] ?? '';
        $this->name = $data['name_EN'] ?? JsonAdapter::camelToEnglish($this->name);
        // $this->data = $data['itemData'] ?? null;
        $shared = isset($data['shared']) ? new ItemSharedData($data['shared']) : null;
        // dump($shared);
        $this->shared_data = $shared->id ?? null;
        // $this->shared_data = $data['shared'] ?? null;
    }*/

    /*public function setNameAttribute($value)
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
    }*/

    public function recipes()
    {
        return $this->belongsToManyThrough(Recipe::class, Resource::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function sharedData()
    {
        return $this->belongsTo(SharedData::class);
    }
}
