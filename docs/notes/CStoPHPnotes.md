<!-- MarkdownTOC -->

- [Game Source](#game-source)
- [Fake Enums](#fake-enums)
    - [ItemType](#itemtype)
    - [AnimationState](#animationstate)
    - [SkillType](#skilltype)
    - [StatusAttribute](#statusattribute)
    - [PieceCategory](#piececategory)
- [Classes](#classes)
    - [ItemCollection](#itemcollection)
    - [Item](#item)
        - [Properties](#properties)
    - [ItemSharedData](#itemshareddata)
        - [Properties](#properties-1)
        - [Methods](#methods)
    - [Recipe](#recipe)
        - [Properties](#properties-2)
        - [Methods](#methods-1)
    - [Resource](#resource)
        - [Properties](#properties-3)
        - [Methods](#methods-2)
    - [CraftingStation](#craftingstation)
        - [ItemDrop.ItemData.SharedData JSON](#itemdropitemdatashareddata-json)
        - [Recipe JSON](#recipe-json)

<!-- /MarkdownTOC -->


# Game Source
- ObjectDB
    - Seems to be an item (or stack of items?) that has been dropped from inventory (or is droppable?)
- ItemDrop
    - ItemData
        - Seems to be info unique to a specific instance of a dropped item (or stack)
    - SharedData
        - Seems to be info common to any instance of a dropped item (or stack) 
        - [JSON]()
- Recipe
    - [JSON]()
- CraftingStation
- Piece    
    - Requirement

---

# Fake Enums

Until PHP 8.1... :)

## ItemType
```php
abstract class ItemType
{
    const NONE = 0;
    const MATERIAL = 1;
    const CONSUMABLE = 2;
    const ONEHANDEDWEAPON = 3;
    const BOW = 4;
    const SHIELD = 5;
    const HELMET = 6;
    const CHEST = 7;
    const AMMO = 9;
    const CUSTOMIZATION = 10;
    const LEGS = 11;
    const HANDS = 12;
    const TROPHIE = 13;
    const TWOHANDEDWEAPON = 14;
    const TORCH = 0xf;
    const MISC = 0x10;
    const SHOULDER = 17;
    const UTILITY = 18;
    const TOOL = 19;
    const ATTACH_ATGEIR = 20;
}
```

## AnimationState
```php
abstract class AnimationState
{
    const UNARMED       = 'Unarmed';
    const ONEHANDED     = 'OneHanded';
    const TWOHANDEDCLUB = 'TwoHandedClub';
    const BOW           = 'Bow';
    const SHIELD        = 'Shield';
    const TORCH         = 'Torch';
    const LEFTTORCH     = 'LeftTorch';
    const ATGEIR        = 'Atgeir';
    const TWOHANDEDAXE  = 'TwoHandedAxe';
    const FISHINGROD    = 'FishingRod';
}
```

## SkillType
```php
abstract class SkillType 
{
    const NONE        = 0;
    const SWORDS      = 1;
    const KNIVES      = 2;
    const CLUBS       = 3;
    const POLEARMS    = 4;
    const SPEARS      = 5;
    const BLOCKING    = 6;
    const AXES        = 7;
    const BOWS        = 8;
    const FIREMAGIC   = 9;
    const FROSTMAGIC  = 10;
    const UNARMED     = 11;
    const PICKAXES    = 12;
    const WOODCUTTING = 13;
    const JUMP        = 100;
    const SNEAK       = 101;
    const RUN         = 102;
    const SWIM        = 103;
    const ALL         = 999;
}
```

## StatusAttribute
```php
abstract class StatusAttribute
{
    const NONE = 0;
    const COLDRESISTANCE = 1;
    const DOUBLEIMPACTDAMAGE = 2;
    const SAILINGPOWER = 4;
}
```

## PieceCategory
```php
abstract class PieceCategory
{
    const MISC = 0;
    const CRAFTING = 1;
    const BUILDING = 2;
    const FURNITURE = 3;
    const MAX = 4;
    const ALL = 100;
}
```

---

# Classes 

## ItemCollection

A group of [`Item`s](#item)

## Item

> Analogous to C# `ItemDrop` combined with `ItemData`

### Properties
```php
string name // InternalID
// Instanced ItemData
// int quality // current quality
// int variant  // current variant
// int durability // current durability
ItemSharedData shared_data
```

## ItemSharedData

### Properties
```php
name
name_EN // default human readable description
description
description_EN // default human readable description -- MAY HAVE XML (see: Amber)
AnimationState animationState // how you equip item
ammoType
armor
attackForce
backstabBonus
blockable
blockPower
blockPowerPerLevel
canBeReparied
deflectionForce
deflectionForcePerLevel
// destroyBroken
// string dlc
dodgeable
durabilityDrain
durabilityPerLevel
equipDuration // how long it takes to equip item after selected
food // total HP granted when eaten
foodBurnTime // effects duration
// foodColor rgba(255,255,255,255)
foodRegen // HP per tick
foodStamina // total stamina granted when eaten
// bool helmetHideHair
// AnimationState holdAnimationState
// holdDurationMin
// holdStaminaDrain
maxDurability
maxQuality // max upgradeable level
maxStackSize // max number you can stack
// movementModifier
// bool questItem
// setName
// setSize
bool teleportable
// timedBlockBonus
// toolTier
// bool useDurability
// useDurabilityDrain
value
variants
weight // weight of single item
// array damageModifiers
SkillType skillType // 
ItemType itemType // 
// attackStatusEffect
// consumeStatusEffect
// equipStatusEffect
// setStatusEffect
Recipe recipe // recipe if craftable
```

### Methods

---

## Recipe

### Properties
```php
name
amount // the number of items created from the recipe
enabled // usable?
minStationLevel // minimum station level needed to create item
CraftingStation craftingStation // station used to create item
// repairStation // seems unused so far
array Resource resources
```

### Methods
```php
/**
 * calculate the required station level for this item based on its quality
 * and the minimum station level
 * 
 * @param int $quality quality level of item
 *
 * @return CraftingStation
 */
public function GetRequiredStationLevel(int $quality) : int

```

## Resource 

> Analogous to C# `Requirement`

### Properties
```cs
public ItemDrop m_resItem;
public int m_amount = 1;
public int m_amountPerLevel = 1;
public bool m_recover = true;
```

### Methods
```cs
public int GetAmount(int qualityLevel)
{
    if (qualityLevel <= 1)
    {
        return m_amount;
    }
    return (qualityLevel - 1) * m_amountPerLevel;
}
```


## CraftingStation

---

### ItemDrop.ItemData.SharedData JSON
```
{
    "name":"Amber",
    "m_itemData":{
        "m_quality":1,
        "m_variant":0,
        "m_durability":100,
        "m_shared":{
            "m_name":"$item_amber",
            "m_name_EN":"Amber",
            "m_description":"$item_amber_description",
            "m_description_EN":"<color=yellow>Valuable</color>",
            "m_aiAttackInterval":2,
            "m_aiAttackMaxAngle":5,
            "m_aiAttackRange":2,
            "m_aiAttackRangeMin":0,
            "m_aiPrioritized":false,
            "m_aiTargetType":"Enemy",
            "m_aiWhenFlying":true,
            "m_aiWhenSwiming":true,
            "m_aiWhenWalking":true,
            "m_animationState":"OneHanded",
            "m_ammoType":"",
            "m_armor":10,
            "m_attackForce":50,
            "m_backstabBonus":4,
            "m_blockable":false,
            "m_blockPower":10,
            "m_blockPowerPerLevel":0,
            "m_canBeReparied":true,
            "m_deflectionForce":0,
            "m_deflectionForcePerLevel":0,
            "m_destroyBroken":true,
            "m_dlc":"",
            "m_dodgeable":false,
            "m_durabilityDrain":0,
            "m_durabilityPerLevel":50,
            "m_equipDuration":1,
            "m_food":0,
            "m_foodBurnTime":0,
            "m_foodColor":"rgba(255, 255, 255, 255)",
            "m_foodRegen":0,
            "m_foodStamina":0,
            "m_helmetHideHair":true,
            "m_holdAnimationState":"",
            "m_holdDurationMin":0,
            "m_holdStaminaDrain":0,
            "m_maxDurability":100,
            "m_maxQuality":1,
            "m_maxStackSize":20,
            "m_movementModifier":0,
            "m_questItem":false,
            "m_setName":"",
            "m_setSize":0,
            "m_teleportable":true,
            "m_timedBlockBonus":1.5,
            "m_toolTier":0,
            "m_useDurability":false,
            "m_useDurabilityDrain":1,
            "m_value":5,
            "m_variants":0,
            "m_weight":0.100000001490116,
            "m_damageModifiers":
            [

            ],
            "m_trophyPos":
            {
                "x":0,
                "y":0
            },
            "m_skillType":"Swords",
            "m_itemType":"Material",
            "m_attackStatusEffect":null,
            "m_consumeStatusEffect":null,
            "m_equipStatusEffect":null,
            "m_setStatusEffect":null
        } // end sharedData
    } // end itemData
} // end itemdrop
```

### Recipe JSON
```
{
    "name":"Recipe_ArmorBronzeLegs",
    "m_amount":1,
    "m_enabled":true,
    "m_minStationLevel":1,
    "m_craftingStation":
    {
        "name":"forge",
        "m_name":"$piece_forge",
        "m_name_EN":"Forge"
    },
    "m_repairStation":null,
    "m_resources":
    [{
        "m_amount":5,
        "m_amountPerLevel":3,
        "m_recover":true,
        "m_resItem":
        {
            "name":"Bronze",
            "m_itemData":null
        }
    },
    {
        "m_amount":2,
        "m_amountPerLevel":0,
        "m_recover":true,
        "m_resItem":
        {
            "name":"DeerHide",
            "m_itemData":null
        }
    }]
}
```
