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
    protected $guarded = [];

    public $item; // Item
    /*public int $id;
    public int $amount = 1;
    public int $amountPerLevel = 1;*/

    public function __construct($data=null)
    {
        // dump("BUILDING A RESOURCE!");
        // dump($data);
        if (is_object($data)) {
            dump("Resource exists as $data, aborting creation");
            // we already made it...
            return;
        }
        $this->amount = $data['amount'] ?? 1;
        $this->amountPerLevel = $data['amountPerLevel'] ?? 1;
        if (isset($data['item'])) {
            if (is_object($data['item'])) {
                $this->item = $data['item'];
            } else {
                $this->item = JsonAdapter::createObject('item', $data['item']) ?? null;
            }
        } else {
            $this->item = null;
        }
    }

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

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class);
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
