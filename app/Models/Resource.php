<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    public $resItem; // Item
    public int $amount = 1;
    public int $amountPerLevel = 1;

    public function __construct($amount=1, $amountPerLevel=1, $resItem=null)
    {
        $this->amount = $amount;
        $this->amountPerLevel = $amountPerLevel;
        $this->resItem = $resItem;
    }

    public function getAmount(int $qualityLevel) : int
    {
        if ($qualityLevel <= 1) {
            return $this->amount;
        }
        return ($qualityLevel - 1) * $this->amountPerLevel;
    }
}
