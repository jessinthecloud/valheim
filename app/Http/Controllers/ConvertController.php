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
                // attach to item
                $item->sharedData()->associate($shared_data);
                $item->save();
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
                    // attach to item
                    $shared_data->setStatusEffect()->associate($status_effect);
                    $shared_data->save();
                }

                // TODO: set damages
                // TODO: set damages_per_level
            } // shared data
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
            $recipe_info['name'] = Recipe::name_EN($recipe_info['name']);
            $recipe_info['slug'] = Str::slug($recipe_info['name']);
            $recipe_info['raw_slug'] = Str::slug(Recipe::name_EN($recipe_info['raw_name']));
            $recipe = Recipe::updateOrCreate(
                ['slug'=>$recipe_info['slug']],
                $recipe_info
            );
            // TODO: setup resources
            if (!empty($recipe_info['resources'])) {
                foreach ($recipe_info['resources'] as $resource_info) {
                    $resource = Resource::create(
                        $resource_info
                    );
                    // attach resource to recipe
                    $resource->recipe()->associate($recipe);
                    $resource->save();

                    $item = Item::where('name', $resource_info['name'])->first();
                    $resource->item()->associate($item);
                    $resource->save();
                } // end each resource
            }
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
