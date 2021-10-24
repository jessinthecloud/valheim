<?php

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairStation extends CraftingStation
{
    use HasFactory;
    
    // custom db table name
    protected $table = 'crafting_stations';

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [];
}
