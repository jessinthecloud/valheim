<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedData extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        'attack_status_effect_id',
        'consume_status_effect_id',
        'equip_status_effect_id',
        'set_status_effect_id',
        'damages',
        'damages_per_level',
    ];

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    public const RELATION_INDICES = [
        'status_effects' => [
            'method' => 'statusEffects',
            'class' => StatusEffect::class,
        ],
    ];
    
    public function items()
    {
        return $this->hasMany(Item::class);
    }
    
    

    public function attackStatusEffect()
    {
        return $this->belongsTo(StatusEffect::class, 'attack_status_effect_id');
    }

    public function consumeStatusEffect()
    {
        return $this->belongsTo(StatusEffect::class, 'consume_status_effect_id');
    }

    public function equipStatusEffect()
    {
        return $this->belongsTo(StatusEffect::class, 'equip_status_effect_id');
    }

    public function setStatusEffect()
    {
        return $this->belongsTo(StatusEffect::class, 'set_status_effect_id');
    }
}
