<?php

namespace App\Models\Items\Craftables\Items;

use App\Models\Items\SharedData;
use App\Models\Items\Traits\HasArmor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Armor extends CraftableItem
{
    use HasFactory, HasArmor;

    protected $table = 'items';

    // https://remixicon.com/
    public const ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M3.783 2.826L12 1l8.217 1.826a1 1 0 0 1 .783.976v9.987a6 6 0 0 1-2.672 4.992L12 23l-6.328-4.219A6 6 0 0 1 3 13.79V3.802a1 1 0 0 1 .783-.976z"/></svg>';

// -- SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('armor', function (Builder $builder) {
            $builder->armor();
        });
    }

// -- MISC -----------------------------------------------------

    /**
     * Need this so we can use it to check for sharedData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sharedData()
    {
        return $this->belongsTo( SharedData::class );
    }

}
