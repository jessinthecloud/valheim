<?php

namespace App\Models\Items\Craftables\Items;

use App\Enums\ItemType;
use App\Models\Items\Contracts\IsItem;
use App\Models\Items\Item;
use App\Models\Items\Contracts\IsCategorizable;
use App\Models\Items\Contracts\IsCraftable;
use App\Models\Items\SharedData;
use App\Models\Items\Traits\HasArmor;
use App\Models\Items\Traits\HasAttacks;
use App\Models\Items\Traits\HasRecipe;
use App\Models\Items\Traits\HasSharedData;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CraftableItem extends Item implements IsItem, IsCraftable, IsCategorizable
{
    use HasFactory, HasSharedData, HasRecipe, HasAttacks, HasArmor;
    
    // converter needs table without instantiating 
    public const TABLE = 'items';

    protected $table = self::TABLE;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'sharedData',
    ];

// -- RELATIONSHIPS -----------------------------------------------------

}
