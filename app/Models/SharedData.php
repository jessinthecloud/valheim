<?php

namespace App\Models;

use App\JsonAdapter;
use App\Enums\AnimationState;
use App\Enums\SkillType;
use App\Enums\ItemType;
use App\Models\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedData extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [
        'attackStatusEffect',
        'consumeStatusEffect',
        'equipStatusEffect',
        'setStatusEffect',
    ];
    /*
        vars to remove if present in JSON
        -- prevents table and relationship checking
     */
    public static $ignore = [
        'm_aiAttackInterval',
        'm_aiAttackMaxAngle',
        'm_aiAttackRange',
        'm_aiAttackRangeMin',
        'm_aiPrioritized',
        'm_aiTargetType',
        'm_aiWhenFlying',
        'm_aiWhenSwiming',
        'm_aiWhenWalking',
        'm_foodColor',
        'm_setName',
        'm_setSize',
        'm_damageModifiers',
        'm_trophyPos',
    ];

    public static $ignoreInTable = [
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
