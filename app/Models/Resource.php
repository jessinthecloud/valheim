<?php

namespace App\Models;

use App\Http\Controllers\JsonAdapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    public int $id;
    public $resItem; // Item
    public int $amount = 1;
    public int $amountPerLevel = 1;

    public function __construct($data=null)
    {
        $this->amount = $data['amount'] ?? 1;
        $this->amountPerLevel = $data['amountPerLevel'] ?? 1;
        if (isset($data['resItem'])) {
            $this->resItem = JsonAdapter::createObject('resItem', $data['resItem']) ?? null;
        } else {
            $this->resItem = null;
        }
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
