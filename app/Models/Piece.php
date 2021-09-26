<?php

namespace App\Models;

use App\Enums\ItemType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        'piece_table_id',
        'requirement_id',
        'crafting_station_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'requirements',
        'pieceTable'
    ];


    // split string into array on uppercase letter and turn it into string
    public static function name_EN($name)
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $name))) ?? $name;
    }

    /**
     * tool that makes the piece
     *
     * @return Eloquent Relationship or Collection of PieceTable
     */
    public function pieceTable()
    {
        return $this->belongsTo(PieceTable::class);
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class);
    }

    public function craftingStation()
    {
        return $this->belongsTo(CraftingStation::class);
    }
}
