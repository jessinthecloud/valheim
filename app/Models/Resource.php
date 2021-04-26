<?php

namespace App\Models;

use App\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = ['item'];
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
        'item'
    ];

    // public $item; // Item
    /*public int $id;
    public int $amount = 1;
    public int $amountPerLevel = 1;*/

    /*public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value;
    }
    public function setAmountPerLevelAttribute($value)
    {
        $this->attributes['amountPerLevel'] = $value;
    }
    public function setResItemAttribute($value)
    {
        $this->attributes['resItem'] = $value;
    }*/

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getAmount(int $qualityLevel) : int
    {
        if ($qualityLevel <= 1) {
            return $this->amount;
        }
        return ($qualityLevel - 1) * $this->amountPerLevel;
    }
}
