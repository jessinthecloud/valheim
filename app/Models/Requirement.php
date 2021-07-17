<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = ['item'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'item',
        'piece',
        'recipes',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class/*, 'recipe_requirement', 'requirement_id', 'recipe_id'*/);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }

    public function getAmount(int $quality_level) : int
    {
        // dump("this amount: {$this->amount}, level: $quality_level, amount per lvl: {$this->amount_per_level}");
        if ($quality_level <= 1) {
            return $this->amount;
        }
        return ($quality_level - 1) * $this->amount_per_level;
    }
}
