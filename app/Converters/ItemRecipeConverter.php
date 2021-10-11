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
        // $data = $relations = keys from $model_class::RELATION_INDICES
        // refactor TODO: loop $data to get the info needed in the functions below, instead of isset() checks
    
        // attach to item being created
        if(isset($data['item_slug'])) {
            $this->attachCreation( $data['item_slug'], $model );
        }

        // attach to crafting station
        if (isset($data['raw_crafting_station_name'])) {
            $this->attachCraftingDevice($data['raw_crafting_station_name'], $model, CraftingStation::class);
        }

        // attach to repair station
        if (isset($data['raw_repair_station_name'])) {
            $this->attachCraftingDevice($data['raw_repair_station_name'], $model, RepairStation::class);
        }

        // create / attach recipe requirements
        if (!empty($data['requirements'])) {
            $this->attachRequirements( $data['requirements'], $model );
        }
    }

    protected function attachCreation($data, $model)
    {
        $this->attachSingleRelation($data, $model, 'creation', Item::class, 'slug');
    }

    protected function attachCraftingDevice($data, $model, string $device_class)
    {
        $this->attachSingleRelation($data, $model, Str::camel($device_class), $device_class, 'raw_name');
    }

    protected function attachRequirements($data, $model)
    {
        // TODO: loop requirements & create

        $requirement = $this->convertRelated($data, Requirement::class);

        // TODO: attach requirement to relevant item (instead of inserting randomly)

    }

    
}