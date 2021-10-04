<?php

namespace App\Converters;

use App\Models\SharedData;

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
            $shared_data = $this->convertSharedData([$data['shared_data']])->first();
//dump('converted shared data ', $shared_data->first());
            $model->sharedData()->associate($shared_data);
            $model->save();
        }
    }
    
    protected function convertSharedData($shared_data)
    {
        $shared_data = $this->prepareData($shared_data, SharedData::class);
        return $this->create($shared_data, SharedData::class);
    }
}