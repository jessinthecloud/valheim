<?php

namespace App\Models\Items;

use App\Enums\ItemType;
use App\Http\ImageFetcher;
use App\Models\Items\Contracts\CanBeIngredient;
use App\Models\Items\Contracts\IsCategorized;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractItem extends Model implements CanBeIngredient, IsCategorized
{
    use HasFactory;
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [];

// -- RELATIONSHIPS -----------------------------------------------

    /**
     * @required by CanBeIngredient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ingredientForItems()
    {
        return $this->hasManyThrough( ItemRecipe::class, Requirement::class, 'item_id',  'creation_id');
    }
    
    /**
     * @required by CanBeIngredient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function ingredientForPieces()
    {
        return $this->hasManyThrough( PieceRecipe::class, Requirement::class, 'item_id',  'creation_id');
    }

// -- MISC -----------------------------------------------

    // split string into array on uppercase letter and turn it into string
    public static function niceName( $name )
    {
        return trim( implode( ' ', preg_split( '/(?=[A-Z])/', $name ) ) ) ?? $name;
    }

    public function image( ImageFetcher $fetcher )
    {
        return $fetcher->fetchImageHtmlString( Str::snake( $this->name ) );
    }
}
