<?php

namespace App\Converters;

use App\Models\SharedData;
use App\Models\StatusEffect;

class ItemConverter extends CraftableConverter
{
    /**
     * Attach relationship data to the inserted entity 
     * (is just sharedData for Item)
     * 
     * @param $data
     */
    protected function attachDataToModel($data, $model)
    {
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
            
            /* 
            "status_effects":[
                {
                    "type":"set",
                    "var_name":"$se_trollseteffect_name",
                    "raw_name":"Sneaky",
                    "true_name":"SetEffect_TrollArmor"
                }
            ]
            "status_effects":[
                {
                    "type":"consume",
                    "var_name":"$item_barleywine",
                    "raw_name":"Fire resistanc
                    e barley wine","true_name":"Potion_barleywine"
                }
            ]
            "status_effects":[
                {
                    "type":"equip",
                    "var_name":"$item_beltstrength",
                    "raw_name":"Megingjord",
                    "true_name":"BeltStrength"
                }
            ]
            "status_effects":[
                {
                    "type":"attack",
                    "var_name":"$se_harpooned_name",
                    "raw_name":"Harpooned",
                    "true_name":"Harpooned"
                }
            ]
            */
        }
    }
    
    protected function convertRelated(array $related_data, string $related_class)
    {
        $related_data = $this->prepareData($related_data, $related_class);
        return $this->create($related_data, $related_class);
    }
}