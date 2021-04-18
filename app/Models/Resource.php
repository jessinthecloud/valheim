<?php

namespace App\Models;

use App\Http\Controllers\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*public int $id;
    public $resItem; // Item
    public int $amount = 1;
    public int $amountPerLevel = 1;*/

    public function __construct($data)
    {
        $this->amount = $data['amount'] ?? 1;
        $this->amountPerLevel = $data['amountPerLevel'] ?? 1;
        if (isset($data['resItem'])) {
            $this->resItem = JsonAdapter::createObject('resItem', $data['resItem']) ?? null;
        } else {
            $this->resItem = null;
        }
    }

    public function setAmountAttribute($value)
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
    }

    public function item()
    {
        return $this->hasOne(Item::class);
    }

    public function getAmount(int $qualityLevel) : int
    {
        if ($qualityLevel <= 1) {
            return $this->amount;
        }
        return ($qualityLevel - 1) * $this->amountPerLevel;
    }
}
