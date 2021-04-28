<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CraftingStation extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [];

    public function slug()
    {
        return Str::slug($this->name);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
