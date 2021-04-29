<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusEffect extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        // ignore

        // end ignore
    ];
    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [
        "m_flashIcon",
        "m_cooldownIcon",
        "m_ttl",
        "m_activationAnimation",
    ];

    public function sharedData()
    {
        return $this->hasMany(SharedData::class);
    }
}
