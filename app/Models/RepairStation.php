<?php

namespace App\Models;

use App\Models\CraftingStation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairStation extends CraftingStation
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
}
