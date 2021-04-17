<!-- MarkdownTOC -->

- [Enums](#enums)
    - [ItemType](#itemtype)
    - [AnimationState](#animationstate)
    - [AiTarget](#aitarget)
    - [SkillType](#skilltype)
    - [StatusAttribute](#statusattribute)
    - [AttackType](#attacktype)
    - [HitPointType](#hitpointtype)
    - [PieceCategory](#piececategory)
    - [ComfortGroup](#comfortgroup)
- [Attributes](#attributes)
- [Classes](#classes)
    - [ObjectDB](#objectdb)
        - [Properties](#properties)
        - [Methods](#methods)
    - [ItemDrop](#itemdrop)
        - [Properties](#properties-1)
        - [Methods](#methods-1)
    - [ItemData](#itemdata)
        - [Enums](#enums-1)
        - [Properties](#properties-2)
        - [Methods](#methods-2)
    - [SharedData](#shareddata)
        - [Properties](#properties-3)
    - [Recipe](#recipe)
        - [Properties](#properties-4)
        - [Methods](#methods-3)
    - [CraftingStation](#craftingstation)
        - [Methods](#methods-4)
    - [Piece](#piece)
        - [Enums](#enums-2)
        - [Properties](#properties-5)
        - [Methods](#methods-5)
    - [Requirement](#requirement)
        - [Properties](#properties-6)
        - [Methods](#methods-6)

<!-- /MarkdownTOC -->


<div id="user-content-text-body" markdown="1">


# Enums 

## ItemType
```cs
// ItemDrop.ItemData.ItemType
public enum ItemType
{
    None = 0,
    Material = 1,
    Consumable = 2,
    OneHandedWeapon = 3,
    Bow = 4,
    Shield = 5,
    Helmet = 6,
    Chest = 7,
    Ammo = 9,
    Customization = 10,
    Legs = 11,
    Hands = 12,
    Trophie = 13,
    TwoHandedWeapon = 14,
    Torch = 0xF,
    Misc = 0x10,
    Shoulder = 17,
    Utility = 18,
    Tool = 19,
    Attach_Atgeir = 20
}
```

## AnimationState
```cs
public enum AnimationState
{
    Unarmed,
    OneHanded,
    TwoHandedClub,
    Bow,
    Shield,
    Torch,
    LeftTorch,
    Atgeir,
    TwoHandedAxe,
    FishingRod
}
```

## AiTarget
```cs
public enum AiTarget
{
    Enemy,
    FriendHurt,
    Friend
}
```

## SkillType
```cs
// Skills.SkillType
public enum SkillType
{
    None = 0,
    Swords = 1,
    Knives = 2,
    Clubs = 3,
    Polearms = 4,
    Spears = 5,
    Blocking = 6,
    Axes = 7,
    Bows = 8,
    FireMagic = 9,
    FrostMagic = 10,
    Unarmed = 11,
    Pickaxes = 12,
    WoodCutting = 13,
    Jump = 100,
    Sneak = 101,
    Run = 102,
    Swim = 103,
    All = 999
}
```

## StatusAttribute
```cs
// StatusEffect.StatusAttribute
public enum StatusAttribute
{
    None = 0,
    ColdResistance = 1,
    DoubleImpactDamage = 2,
    SailingPower = 4
}
```

## AttackType
```cs 
public enum AttackType
{
    Horizontal,
    Vertical,
    Projectile,
    None,
    Area,
    TriggerProjectile
}
```

## HitPointType
```cs
public enum HitPointType
{
    Closest,
    Average,
    First
}
```

## PieceCategory
```cs
public enum PieceCategory
{
    Misc = 0,
    Crafting = 1,
    Building = 2,
    Furniture = 3,
    Max = 4,
    All = 100
}
```

## ComfortGroup
```cs
public enum ComfortGroup
{
    None,
    Fire,
    Bed,
    Banner,
    Chair
}
```

---

# Attributes

```cs
// in ItemDrop.ItemData.SharedData
[Header("Food settings")]
[Header("Armor settings")]
[Header("Shield settings")]
[Header("Weapon")]
[Header("Attacks")]
[Header("Durability")]
[Header("Hold")]
[Header("Ammo")]
[Header("AI")]
[Header("Effects")]
[Header("Consumable")]
// in Recipe
[Header("Requirements")]
// in Piece
[Header("Basic stuffs")]
[Header("Comfort")]
[Header("Placement rules")]
[BitMask(typeof(Heightmap.Biome))]
[Header("Effects")]
[Header("Requirements")]
// in Piece.Requirement
[Header("Resource")]
[Header("Item")]
[Header("Piece")]
```

---

# Classes

## ObjectDB

> ObjectDB : MonoBehaviour

Seems to be a collection (stack?) of items. 

May need to rethink the structure needed as it pertains to [`ItemDrop`](#ItemDrop)

### Properties

```cs
private static ObjectDB instance;
List<StatusEffect> StatusEffects = new List<StatusEffect>();
List<GameObject> items = new List<GameObject>();
List<Recipe> recipes = new List<Recipe>();
private Dictionary<int, GameObject> itemByHash = new Dictionary<int, GameObject>();
static ObjectDB instance => instance;
```

### Methods

```cs
public StatusEffect GetStatusEffect(string name);

public GameObject GetItemPrefab(string name);

public GameObject GetItemPrefab(int hash);

public int GetPrefabHash(GameObject prefab);

public List<ItemDrop> GetAllItems(ItemDrop.ItemData.ItemType type, string startWith);

public Recipe GetRecipe(ItemDrop.ItemData item);

```

## ItemDrop

> ItemDrop : MonoBehaviour, Hoverable, Interactable

Contains [`ItemData`](#ItemData) class, which contains [enums](#Enums)

Seems to be an item (or stack of items?) that has been dropped from inventory (or is droppable?)

### Properties

```cs
private int myIndex = -1;
bool autoPickup = true;
bool autoDestroy = true;
ItemData itemData = new ItemData();
private float lastOwnerRequest;
private float spawnTime;
private const double autoDestroyTimeout = 3600.0;
private const double autoPickupDelay = 0.5;
private const float autoDespawnBaseMinAltitude = -2f;
private const int autoStackTreshold = 200;
private const float autoStackRange = 4f;
private static int itemMask = 0;
private bool haveAutoStacked;
```
### Methods

```cs
public string GetHoverText();

public string GetHoverName();

private string GetPrefabName(string name);

public void SetStack(int stack);

public bool RemoveOne();

```

## ItemData

> ItemDrop.ItemData
>
> [Serializable]

Contains [`SharedData`](#SharedData) class

Seems to be info unique to an instance of a dropped item (or stack)

### Enums

 - [ItemType](#ItemType)
 - [AnimationState](AnimationState)
 - [AiTarget](#AiTarget)

### Properties
```cs
int stack = 1;
float durability = 100f;
int quality = 1;
int variant;
SharedData shared;

[NonSerialized]
long crafterID;

[NonSerialized]
string crafterName = "";

[NonSerialized]
bool equiped;

[NonSerialized]
GameObject dropPrefab;

[NonSerialized]
float lastAttackTime;

[NonSerialized]
GameObject lastProjectile;
```

### Methods

```cs
public bool IsEquipable();

public bool IsWeapon();

public bool HavePrimaryAttack();

public bool HaveSecondaryAttack();

public float GetArmor();

public float GetArmor(int quality);

public int GetValue();

public float GetWeight();

public HitData.DamageTypes GetDamage();

public float GetDurabilityPercentage();

public float GetMaxDurability();

public float GetMaxDurability(int quality);

public HitData.DamageTypes GetDamage(int quality);

public float GetBaseBlockPower();

public float GetBaseBlockPower(int quality);

public float GetBlockPower(float skillFactor);

public float GetBlockPower(int quality, float skillFactor);

public float GetBlockPowerTooltip(int quality);

public float GetDeflectionForce();

public float GetDeflectionForce(int quality);

public string GetTooltip();

public Sprite GetIcon();

private static void AddHandedTip(ItemData item, StringBuilder text);

public static string GetTooltip(ItemData item, int qualityLevel, bool crafting);

private string GetStatusEffectTooltip();

private string GetSetStatusEffectTooltip();

private string GetProjectileTooltip(int itemQuality);
```

## SharedData

> ItemDrop.ItemData.SharedData
>
> [Serializable]

Seems to be info common to any instances of a dropped item (or stack) 

### Properties
```cs
string name = "";
string dlc = "";
ItemType itemType = ItemType.Misc;
Sprite[] icons = new Sprite[0];
ItemType attachOverride;

[TextArea]
string description = "";

int maxStackSize = 1;
int maxQuality = 1;
float weight = 1f;
int value;
bool teleportable = true;
bool questItem;
float equipDuration = 1f;
int variants;
PieceTable buildPieces;
bool centerCamera;
string setName = "";
int setSize;
StatusEffect setStatusEffect;
StatusEffect equipStatusEffect;
float movementModifier;

[Header("Food settings")]
float food;

float foodStamina;
float foodBurnTime;
float foodRegen;
Color foodColor = Color.white;

[Header("Armor settings")]
Material armorMaterial;

bool helmetHideHair = true;
float armor = 10f;
float armorPerLevel = 1f;
List<HitData.DamageModPair> damageModifiers = new List<HitData.DamageModPair>();

[Header("Shield settings")]
float blockPower = 10f;

float blockPowerPerLevel;
float deflectionForce;
float deflectionForcePerLevel;
float timedBlockBonus = 1.5f;

[Header("Weapon")]
AnimationState animationState = AnimationState.OneHanded;

Skills.SkillType skillType = Skills.SkillType.Swords;
int toolTier;
HitData.DamageTypes damages;
HitData.DamageTypes damagesPerLevel;
float attackForce = 30f;
float backstabBonus = 4f;
bool dodgeable;
bool blockable;
StatusEffect attackStatusEffect;
GameObject spawnOnHit;
GameObject spawnOnHitTerrain;

[Header("Attacks")]
Attack attack;

Attack secondaryAttack;

[Header("Durability")]
bool useDurability;

bool destroyBroken = true;
bool canBeReparied = true;
float maxDurability = 100f;
float durabilityPerLevel = 50f;
float useDurabilityDrain = 1f;
float durabilityDrain;

[Header("Hold")]
float holdDurationMin = true;

float holdStaminaDrain - true;
string holdAnimationState = "";

[Header("Ammo")]
string ammoType = "";

[Header("AI")]
aiAttackRange = 2f;

aiAttackRangeMin;
aiAttackInterval = 2f;
aiAttackMaxAngle = 5f;
aiWhenFlying = true;
aiWhenWalking = true;
aiWhenSwiming = true;
aiPrioritized;
aiTargetType;

[Header("Effects")]
EffectList hitEffect = new EffectList();

EffectList hitTerrainEffect = new EffectList();
EffectList blockEffect = new EffectList();
EffectList startEffect = new EffectList();
EffectList holdStartEffect = new EffectList();
EffectList triggerEffect = new EffectList();
EffectList trailStartEffect = new EffectList();

[Header("Consumable")]
StatusEffect consumeStatusEffect;
```

## Recipe

> Recipe : ScriptableObject

### Properties

```cs
public ItemDrop item;
// the number of items created from the recipe
public int amount = 1; 
public bool enabled = true;

[Header("Requirements")]
public CraftingStation craftingStation;

public CraftingStation repairStation;
public int minStationLevel = 1;
// Array of resource information pertaining to the recipe requirements
public Piece.Requirement[] resources = new Piece.Requirement[0];
```

### Methods
```cs
public int GetRequiredStationLevel(int quality);

public CraftingStation GetRequiredStation(int quality);

```


## CraftingStation

> CraftingStation : MonoBehaviour, Hoverable, Interactable

```cs
public string name = "";
public Sprite icon;
public float discoverRange = 4f;
public float rangeBuild = 10f;
public bool craftRequireRoof = true;
public bool craftRequireFire = true;
public Transform roofCheckPoint;
public Transform connectionPoint;
public bool showBasicRecipies;
public float useDistance = 2f;
public int useAnimation;
public GameObject areaMarker;
public GameObject inUseObject;
public GameObject haveFireObject;
public EffectList craftItemEffects = new EffectList();
public EffectList craftItemDoneEffects = new EffectList();
public EffectList repairItemDoneEffects = new EffectList();
private const float updateExtensionInterval = 2f;
private float updateExtensionTimer;
private float useTimer = 10f;
private bool haveFire;
private ZNetView nview;
private List<StationExtension> attachedExtensions = new List<StationExtension>();
private static List<CraftingStation> allStations = new List<CraftingStation>();
private static int triggerMask = 0;
```

### Methods 
```cs
public string GetHoverText();

public string GetHoverName();

private List<StationExtension> GetExtensions();

public int GetLevel();

```

## Piece

> Piece : StaticTarget

Contains [`Requirement`](#Requirement)

### Enums
- [PieceCategory](#PieceCategory)
- [ComfortGroup](#ComfortGroup)

### Properties 
```cs
private static int pieceRayMask = 0;
private static Collider[] pieceColliders = (Collider[])(object)new Collider[2000];
private static int ghostLayer = 0;

[Header("Basic stuffs")]
public Sprite icon;

public string name = "";
public string description = "";
public bool enabled = true;
public PieceCategory category;
public bool isUpgrade;

[Header("Comfort")]
public int comfort;

public ComfortGroup comfortGroup;

[Header("Placement rules")]
public bool groundPiece;

public bool allowAltGroundPlacement;
public bool groundOnly;
public bool cultivatedGroundOnly;
public bool waterPiece;
public bool clipGround;
public bool clipEverything;
public bool noInWater;
public bool notOnWood;
public bool notOnTiltingSurface;
public bool inCeilingOnly;
public bool notOnFloor;
public bool noClipping;
public bool onlyInTeleportArea;
public bool allowedInDungeons;
public float spaceRequirement;
public bool repairPiece;
public bool canBeRemoved = true;

[BitMask(typeof(Heightmap.Biome))]
public Heightmap.Biome onlyInBiome;

[Header("Effects")]
public EffectList placeEffect = new EffectList();

[Header("Requirements")]
public string dlc = "";

public CraftingStation craftingStation;
public Requirement[] resources = new Requirement[0];
public GameObject destroyedLootPrefab;
private List<KeyValuePair<Renderer, Material[]>> invalidPlacementMaterials;
private long creator;
private int myListIndex = -1;
private static List<Piece> allPieces = new List<Piece>();
private static int creatorHash = 0;
```

### Methods
```cs
public void SetCreator(long uid);

public long GetCreator();

public bool IsCreator();

public bool IsPlacedByPlayer();
```


## Requirement

> Piece.Requirement
>
> [Serializable]

Seems to be a set (array) of resource information pertaining to a requirement (i.e. `Recipe`)

### Properties

```cs
// item needed
[Header("Resource")]
public ItemDrop resItem;
// number of items needed
public int amount = 1;
// number of items needed for upgrading
[Header("Item")]
public int amountPerLevel = 1;
// whether parts are returned on destroy (?)
[Header("Piece")]
public bool recover = true;
```

### Methods
```cs
// get amount needed for upgrades
// is current quality level - 1 * amountPerLevel
public int GetAmount(int qualityLevel);
```

</div> <!-- /user-content-text-body -->
