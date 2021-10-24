<?php

namespace App\Models\Items\Craftables\Items;

use App\Models\Items\SharedData;
use App\Models\Items\Traits\HasAttacks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Weapon extends CraftableItem
{
    use HasFactory, HasAttacks;

    protected $table = 'items';
    
    // https://remixicon.com/
    public const ICON = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M7.05 13.406l3.534 3.536-1.413 1.414 1.415 1.415-1.414 1.414-2.475-2.475-2.829 2.829-1.414-1.414 2.829-2.83-2.475-2.474 1.414-1.414 1.414 1.413 1.413-1.414zM3 3l3.546.003 11.817 11.818 1.415-1.414 1.414 1.414-2.474 2.475 2.828 2.829-1.414 1.414-2.829-2.829-2.475 2.475-1.414-1.414 1.414-1.415L3.003 6.531 3 3zm14.457 0L21 3.003l.002 3.523-4.053 4.052-3.536-3.535L17.457 3z" fill="rgba(238,238,238,1)"/></svg>';

// -- SCOPES -----------------------------------------------------

    /**
     * Apply this scope to every query
     * made by this model
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('weapon', function (Builder $builder) {
            $builder->weapon();
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
