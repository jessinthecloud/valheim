<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Enums\ItemType;
use App\Enums\AnimationState;
use App\Enums\DamageType;
use App\Enums\SkillType;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ItemController;
use App\Models\Recipe;
use App\Models\Resource;
use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\SharedData;
use App\Models\StatusEffect;

class ConvertController extends Controller
{
    /**
     * convert an item by unqiue name
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function convert($name, $type='')
    {
        dump("Convert: $name");
        if (file_exists('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\\'.strtolower($type).'-list.json')) {
            // remove invalid hex characters
            $contents = json_decode(file_get_contents(
                'G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\\'.strtolower($type).'-list.json',
                true
            ));
        } else {
            dump('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\\'.strtolower($type).'-list.json'." doesn't exist.");
        }
    } // end func convert()

    /**
    *
    * @return \Illuminate\Http\Response
    */
    public function statusEffect()
    {
        echo "CONVERT status_effect";
        // remove invalid hex characters
        $contents = preg_replace('/[\x00-\x1F\x7F]/u', '', file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\status-effects\status-effect-list.json'));
        $status_effects = json_decode($contents, true);

        foreach ($status_effects as $status_effect_info) {
            $status_effect_info['slug'] = Str::slug($status_effect_info['name']);
            $status_effect = StatusEffect::updateOrCreate(
                ['name'=>$status_effect_info['name']],
                $status_effect_info
            );
        } // end foreach status_effect
    }

    /**
    *
    * @return \Illuminate\Http\Response
    */
    public function item($name='')
    {
        echo "CONVERT ITEM $name";
        if (!empty($name)) {
            return $this->convert($name, 'item');
        }

        // decode json file to array
        // remove invalid hex characters
        $contents = preg_replace('/[\x00-\x1F\x7F]/u', '', file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\item-list.json'));
        $items = json_decode(
            $contents,
            true
        );

        foreach ($items as $item_info) {
            $item_info['name'] = Item::name_EN($item_info['name']);
            $item_info['slug'] = Str::slug($item_info['name']);
            $item_info['raw_slug'] = Str::slug(Item::name_EN($item_info['raw_name']));

            $item = Item::updateOrCreate(
                ['slug'=>$item_info['slug']],
                $item_info
            );

            $shared_data_info = $item_info['shared_data'];
            if (!empty($shared_data_info)) {
                // php is being very difficult about getting this value
                $shared_data_info['item_type'] = (new \ReflectionClass(ItemType::class))->getConstant(strtoupper($shared_data_info['item_type']));

                $shared_data = SharedData::updateOrCreate(
                    [
                        'name' => $shared_data_info['name'],
                        'item_type' => $shared_data_info['item_type'],
                    ],
                    $shared_data_info
                );
                // we don't want to attach unless a new resource was created
                if ($shared_data->wasRecentlyCreated) {
                    // attach to item
                    $item->sharedData()->associate($shared_data);
                    $item->save();
                }
                $status_effect_name = null;
                if (!empty($shared_data_info['set_status_effect_name'])) {
                    $status_effect_name = $shared_data_info['set_status_effect_name'];
                } elseif (!empty($shared_data_info['consume_status_effect_name'])) {
                    $status_effect_name = $shared_data_info['consume_status_effect_name'];
                } elseif (!empty($shared_data_info['attack_status_effect_name'])) {
                    $status_effect_name = $shared_data_info['attack_status_effect_name'];
                } elseif (!empty($shared_data_info['equip_status_effect_name'])) {
                    $status_effect_name = $shared_data_info['equip_status_effect_name'];
                }

                if (isset($status_effect_name)) {
                    $status_effect = StatusEffect::updateOrCreate(
                        ['name' => $status_effect_name],
                        ['name' => $status_effect_name]
                    );
                    // we don't want to attach unless a new resource was created
                    if ($status_effect->wasRecentlyCreated) {
                        // attach to item
                        $shared_data->setStatusEffect()->associate($status_effect);
                        $shared_data->save();
                    }
                }

                // TODO: set damages
                // TODO: set damages_per_level
            } // endif shared data
        } // end foreach item
    } // end item

        /**
    *
    * @return \Illuminate\Http\Response
    */
    public function recipe($name='')
    {
        echo "CONVERT RECIPE";
        if (!empty($name)) {
            return $this->convert($name, 'recipe');
        }
        // remove invalid hex characters
        $contents = preg_replace('/[\x00-\x1F\x7F]/u', '', file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\recipe-list.json'));
        $recipes = json_decode($contents, true);
        // dump('RECIPES', $recipes);
        foreach ($recipes as $recipe_info) {
            $recipe_info['name'] = strtoupper($recipe_info['name']) !== "NULL" ? Recipe::name_EN($recipe_info['name']) : Recipe::name_EN($recipe_info['raw_name']);
            $recipe_info['slug'] = Str::slug($recipe_info['name']);
            $recipe_info['raw_slug'] = Str::slug(Item::name_EN($recipe_info['raw_name']));
            $recipe = Recipe::updateOrCreate(
                ['slug'=>$recipe_info['slug']],
                $recipe_info
            );

            if (!empty($recipe_info['resources'])) {
                // make sure we don't already have this resource attached
                $existing_resources = $recipe->resources;
                $recipe_info['resources'] = collect($recipe_info['resources'])->filter(function ($value, $key) use ($existing_resources) {
                    $existing = collect($existing_resources->toArray());
                    // if even one part doesn't match, it's a new resource
                    // Eloquent collection uses contains() differently, so set to regular collection
                    return (!$existing->contains('name', $value['name']) || !$existing->contains('amount', $value['amount']) || !$existing->contains('amount_per_level', $value['amount_per_level']) || !$existing->contains('recover', $value['recover']));
                })->toArray();

                foreach ($recipe_info['resources'] as $resource_info) {
                    $resource_info['slug'] = Str::slug(Item::name_EN($resource_info['name']));

                    $resource = Resource::updateOrCreate(
                        [
                            'name'=>$resource_info['name'],
                            'amount'=>$resource_info['amount'],
                            'amount_per_level'=>$resource_info['amount_per_level'],
                            'recover'=>$resource_info['recover'],
                        ],
                        $resource_info
                    );

                    // attach resource to recipe
                    $resource->recipe()->attach($recipe);
                    $resource->save();

                    // make sure we don't already have this item attached
                    $item = Item::where('slug', $resource_info['slug'])->first();
                    $existing_item = $resource->item;
                    if (isset($existing_item)) {
                        $item = $existing_item->contains($item) ? false : $item;

                        if ($item !== false) {
                            $resource->item()->associate($item);
                            $resource->save();
                        }
                    }
                } // end each resource
            } // endif resources
            // TODO: set crafting station
            // TODO: set repair station
        } // end foreach recipe
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        // convert all
        $this->statusEffect();
        $this->item();
        $this->recipe();
    }
}
