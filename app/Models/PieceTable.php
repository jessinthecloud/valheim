<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PieceTable extends CraftingDevice
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

    // remove Recipe_ prefix
    public static function name_EN($name)
    {
        $name = trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
        return (explode('_', $name)[1]) ?? $name;
    }

    public function craftables()
    {
        return $this->hasManyThrough(Piece::class, PieceRecipe::class);
    }
}
