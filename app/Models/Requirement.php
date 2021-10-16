<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
//        'item'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'item',
        /*'pieces',
        'recipes',*/
    ];

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'raw_name' => [
            'method' => 'item',
            'class' => Item::class,
            'relation' => 'associate',
        ],
    ];

    public function recipes()
    {
        return $this->belongsToMany(ItemRecipe::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function pieces()
    {
        return $this->belongsToMany(Piece::class);
    }

    public function getAmount(int $quality_level=1) : int
    {
        // dump("this amount: {$this->amount}, level: $quality_level, amount per lvl: {$this->amount_per_level}");
        if ($quality_level <= 1) {
            return $this->amount;
        }
        return ($quality_level - 1) * $this->amount_per_level;
    }

    public function recoverable() : bool
    {
        return (bool)$this->recover;
    }
}
