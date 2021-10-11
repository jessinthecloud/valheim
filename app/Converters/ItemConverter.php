<?php

namespace App\Converters;

use App\Models\SharedData;
use App\Models\StatusEffect;

class ItemConverter extends CraftableConverter
{
    /**
     * @required by JsonConverter
     *           
     * Attach relationship data to the inserted entity
     * (is just sharedData for Item)
     *
     * @param $data
     * @param $model
     */
    protected function attachDataToModel($data, $model)
    {
        // TODO: refactor to remove hardcoding data checks
        
//        dump('== attachDataToModel == ',$data, $model);
        if(isset($data['shared_data'])){
            // returns collection of 1
            $shared_data = $this->convertRelated([$data['shared_data']], SharedData::class)->first();
//dump('converted shared data ', $shared_data->first());
            $model->sharedData()->associate($shared_data);
            $model->save();
        }
        
        // status_effects is an array, multiple methods are involved
        if(isset($data['status_effects']) && !empty($data['status_effects'])){
            
            foreach($data['status_effects'] as $status_effect_data){

                // see if they exist
                $effect = StatusEffect::where('true_name', 'like', '%'.$status_effect_data['true_name'].'%')->first();

                if(!isset($effect)){
                    // convert new thing 
                    $effect = $this->convertRelated($status_effect_data, StatusEffect::class)->first();
                }
              
                // attach to model
                $model->{$status_effect_data['type'].'StatusEffect'}()->associate($effect);
                $model->save();
            } // end foreach status effect
        }
    }
}