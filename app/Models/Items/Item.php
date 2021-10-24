<?php

namespace App\Models\Items;

use App\Http\ImageFetcher;
use App\Models\Items\Contracts\CanBeIngredient;
use App\Models\Items\Contracts\IsItem;
use App\Models\Recipes\ItemRecipe;
use App\Models\Recipes\PieceRecipe;
use App\Models\Recipes\Requirement;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Items and Pieces. Item could be craftable or a natural drop
// should really be abstract... but Requirement
// needs a way to get relation to ALL items, not just craftable
class Item extends Model implements CanBeIngredient
{
    use HasFactory;

    // more useful: only lockdown specific fields from being mass-assigned
    // empty array means nothing is locked down
    protected $guarded = [];
    
    protected $table = 'items';

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

    /**
     * Need this so we can use it to check for recipes
     * 
     * items can have multiple recipes for their variants
     * e.g., Bronze and 5 Bronze bars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipes()
    {
        return $this->hasMany( ItemRecipe::class, 'creation_id', 'id' );
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

// -- MISC -----------------------------------------------

    // split string into array on uppercase letter and turn it into string
    public static function niceName( $name ) : string
    {
        return trim( implode( ' ', preg_split( '/(?=[A-Z])/', $name ) ) ) ?? $name;
    }

    public function imageUrl() : string
    {
        return !empty($this->getAttribute('image')) ? asset('storage/images/'.$this->image) : '';
    }

    public function image() : string
    {
        $image = !empty($this->getAttribute('image')) ? asset('storage/images/'.$this->image) : '';

        return '<img src="'.$image.'" alt="'.self::niceName($this->name).'">';
    }

    public function hasRecipes() : bool
    {
        return (!empty($this->recipes->filter()->all()));
    }

    public function hasSharedData() : bool
    {
        return (!empty($this->sharedData));
    }

    /**
     * uniformity for description field
     *
     * @return ?string
     */
    public function description() : ?string
    {
        return ($this->hasSharedData() ? $this->sharedData->description : ( $this->description ?? ''));
    }
}
