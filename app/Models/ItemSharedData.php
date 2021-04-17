<?php

namespace App\Models;

use App\Enums\AnimationState;
use App\Enums\SkillType;
use App\Enums\ItemType;
use App\Models\Recipe;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSharedData extends Model
{
    use HasFactory;

    public $name;
    // default human readable name
    public $name_EN;
    public $description;
    // default human readable description
    // -- MAY HAVE XML (see: Amber);
    public $description_EN;
    // how you equip item
    public AnimationState $animationState;
    public $ammoType;
    public $armor;
    public $attackForce;
    public $backstabBonus;
    public $blockable;
    public $blockPower;
    public $blockPowerPerLevel;
    public $canBeReparied;
    public $deflectionForce;
    public $deflectionForcePerLevel;
    public $dodgeable;
    public $durabilityDrain;
    public $durabilityPerLevel;
    // how long it takes to equip item after selected
    public $equipDuration;
    // total HP granted when eaten
    public $food;
    // effects duration
    public $foodBurnTime;
    // HP per tick
    public $foodRegen;
    // total stamina granted when eaten
    public $foodStamina;
    public $maxDurability;
    // max upgradeable level
    public $maxQuality;
    // max number you can stack
    public $maxStackSize;
    public bool $teleportable;
    public $value;
    public $variants;
    // weight of single item
    public $weight;
    public SkillType $skillType;
    public ItemType $itemType;
    // recipe if craftable
    public Recipe $recipe;

    // destroyBroken
    // string dlc
    // attackStatusEffect
    // consumeStatusEffect
    // equipStatusEffect
    // foodColor rgba(255,255,255,255)
    // bool helmetHideHair
    // AnimationState holdAnimationState
    // holdDurationMin
    // holdStaminaDrain
    // setStatusEffect
    // array damageModifiers
    // timedBlockBonus
    // toolTier
    // bool useDurability
    // useDurabilityDrain
    // movementModifier
    // bool questItem
    // setName
    // setSize
}
