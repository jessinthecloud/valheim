<?php

namespace App\Models\Items;

use App\Models\Items\Craftables\Items\Armor;
use App\Models\Items\Craftables\Items\Consumable;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Craftables\Items\Weapon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedData extends Model
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    public const RELATION_INDICES = [
        'status_effects' => [
            'method' => 'statusEffects',
            'class' => StatusEffect::class,
            'relation' => 'associate',
        ],
    ];
    
    public function item()
    {
        return $this->hasOne(Item::class);
    }

    public function craftableItem()
    {
        return $this->hasOne(CraftableItem::class);
    }

    public function armor()
    {
        return $this->hasOne(Armor::class);
    }

    public function weapon()
    {
        return $this->hasOne(Weapon::class);
    }

    public function consumable()
    {
        return $this->hasOne(Consumable::class);
    }
    
    // return all relations
    public function statusEffects()
    {
        return [
            'attackStatusEffect',
            'consumeStatusEffect',
            'setStatusEffect',
            'equipStatusEffect',
        ];
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
