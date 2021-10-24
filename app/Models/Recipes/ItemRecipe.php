<?php

namespace App\Models\Recipes;

use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Tools\CraftingStation;
use App\Models\Tools\RepairStation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRecipe extends Recipe
{
    use HasFactory;
    
    protected $table = 'item_recipes';

    // Indices of the converted json array that correspond to
    // relationships and should not be directly inserted
    // index_name => relationFunctionName ; to allow array_intersect_key comparison
    public const RELATION_INDICES = [
        'item_slug' => [
            'method' => 'creation',
            'class' => CraftableItem::class,
            'relation' => 'associate',
        ],
        'requirements' => [
            'method' => 'requirements',
            'class' => Requirement::class,
            'relation' => 'attach',
        ],
        'raw_crafting_station_name' => [
            'method' => 'craftingDevice',
            'class' => CraftingStation::class,
            'relation' => 'associate',
        ],
        'raw_repair_station_name' => [
            'method' => 'repairStation',
            'class' => RepairStation::class,
            'relation' => 'associate',
        ],
    ];

// -- RELATIONSHIPS -----------------------------------------------

    // item this recipe creates
    public function creation()
    {
        return $this->belongsTo(CraftableItem::class, 'creation_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(Requirement::class);
    }

    /**
     * tool that makes the item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * Relationship or Collection of PieceTable
     */
    public function craftingDevice()
    {
        return $this->craftingStation();
    }

// -- MISC -----------------------------------------------

    public function icon() : string
    {
        return null !== $this->creation ? $this->creation->icon() : '';
    }

    public function maxQuality() : int
    {
        return (!empty($this->creation) && !empty($this->creation->sharedData)) ? ($this->creation->sharedData->max_quality ?? 1) : 1;
    }

    public function upgrades()
    {
        $upgrades = [];

        // start at the first upgrade level and determine the required item amounts
        for ($i=2; $i<=$this->maxQuality(); $i++) {
        
            $upgrades[$i]= [
                'station' => $this->requiredStation($i)->name,
                'station_level' => $this->requiredStationLevel($i),
            ];
        
            foreach ($this->requirements as $req) {
                if(null === $req->item){
//                    dump('[missing req item]');
                    continue;
                }
                
                $upgrades[$i]['resources'][$req->item->name]= $req->getAmount($i);
            
            } // end foreach
        } // end for

        return $upgrades;
    }

    public function upgradeTotals( $upgrades )
    {
        $resources = collect($upgrades)->pluck('resources')->filter();
        $html = '';
        $totals = $resources->flatMap(function($resource, $key) use ($resources) {
            return collect($resource)->map(function($amount, $item) use ($resources) {
//                dump($amount.' '.$item);
                return $resources->sum($item);
            });
        });
      
        foreach ( $totals as $item => $amount ) {
            if ( $amount > 0 ) {
                $html .= '<strong>' . $amount . '</strong> ' . $item . ', ';
            }
        } // sums
        $html = rtrim($html, ', ');

        // include max station level in totals
        if (isset($upgrades[$this->maxQuality()]) && $upgrades[$this->maxQuality()]['station_level'] > 1) {
            $html .= ' (<strong>Level ' . $upgrades[$this->maxQuality()]['station_level'].' '.$upgrades[$this->maxQuality()]['station'] . '</strong>)';
        }
        
        return $html;
    }
}
