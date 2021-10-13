<?php

namespace App\Converters;

use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\RepairStation;
use App\Models\Requirement;
use Illuminate\Support\Str;

class ItemRecipeConverter extends RecipeConverter
{
    /**
     * @required by JsonConverter
     *           
     * Attach relationship data to the inserted entity
     *
     * @param $data
     * @param $model
     */
    protected function attachDataToModel( $data, $model )
    {
//dump('== attachDataToModel == ',$data, $model);

        // $data = $relations = keys from $model_class::RELATION_INDICES
        // refactor TODO: loop $data to get the info needed in the functions below, instead of isset() checks
    
        // attach to item being created
         if(isset($data['item_slug'])) {
            // some items don't exist
            if(in_array($data['item_slug'], ['recipe-adze'])){
dump('item '.$data['item_slug'].' doesn\'t exist, skip');            
                return null;
            }
//dump($data); 
            $this->attachCreation( $data['item_slug'], $model, $data );
        }

        // attach to crafting station
        if (isset($data['raw_crafting_station_name'])) {
            $this->attachCraftingDevice($data['raw_crafting_station_name'], $model, CraftingStation::class, $data);
        }

        // attach to repair station
        if (isset($data['raw_repair_station_name'])) {
            $this->attachCraftingDevice($data['raw_repair_station_name'], $model, RepairStation::class, $data);
        }

        // create / attach recipe requirements
        if (!empty($data['requirements'])) {
            $this->attachRequirements( $data['requirements'], $model );
        }
        
        // requirement has item attached
        if(isset($data['item'])){
            $this->attachSingleRelation($data['item'], $model, 'item', Item::class, 'slug', $data);
        }
    }

    protected function attachCreation($value, $model, $data)
    {
    dump('attach creation');
        $this->attachSingleRelation($value, $model, 'creation', Item::class, 'slug', $data);
    }

    protected function attachCraftingDevice($value, $model, string $device_class, $data)
    {
        dump('attach crafting device');
        $this->attachSingleRelation($value, $model, Str::camel(Str::afterLast($device_class, '\\')), $device_class, 'raw_name', $data);
    }

    protected function attachRequirements($data, $model)
    {
dump('attach requirements');

        // loop requirements & create
        foreach($data as $i => $requirement){
dump('requirement '.$i.' data', $requirement);

            // attach requirement to relevant item (instead of inserting randomly)
            $requirement = $this->convertRelated($requirement, Requirement::class);
dump('attach requirement', $requirement, 'to', $model);            
            $model->requirements()->attach($requirement);
            $model->save();
        }

//ddd('=== after attachments ===', $model);

    }

    
}