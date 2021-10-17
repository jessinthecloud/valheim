<?php

namespace App\Models\Craftables\Items;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusEffect extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [

    ];

    public function sharedData()
    {
        return $this->hasMany(SharedData::class);
    }
}
