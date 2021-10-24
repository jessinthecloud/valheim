<?php

namespace App\Models\Items\Craftables\Items;

use App\Models\Items\SharedData;
use App\Models\Items\Traits\Consumable as IsConsumable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consumable extends CraftableItem
{
    use HasFactory, IsConsumable;

    protected $table = 'items';
    
    // https://remixicon.com/    
    public const ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M21 2v20h-2v-8h-3V7a5 5 0 0 1 5-5zM9 13.9V22H7v-8.1A5.002 5.002 0 0 1 3 9V3h2v7h2V3h2v7h2V3h2v6a5.002 5.002 0 0 1-4 4.9z"/></svg>';

// -- SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('consumable', function (Builder $builder) {
            $builder->consumable();
        });
    }

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
