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
use App\Models\Requirement;
use App\Models\CraftingStation;
use App\Models\Item;
use App\Models\SharedData;
use App\Models\StatusEffect;

class ConvertController extends Controller
{
    /**
     * Remove invalid hex characters from a string
     *
     * @param  string $string string to sanitize
     *
     * @return string         sanitized string
     */
    public function removeInvalidHex(string $string)
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }

    public function convertJsonList(string $json_filepath, string $class, array $unique_keys, array $related_keys=[])
    {
        $contents = $this->removeInvalidHex(file_get_contents($json_filepath));
        $objects = json_decode($contents, true);
        dump($objects);

        foreach ($objects as $object_info) {
            $object_info = $this->createAllNames($object_info);
            dump($object_info);
            // setup info for updateOrCreate()
            $check_columns = [];
            foreach ($unique_keys as $keyname) {
                $check_columns [$keyname]= $object_info[$keyname];
            }
            $object = $class::updateOrCreate(
                $check_columns,
                $object_info
            );

            // check for related Models
            if (!empty($related_keys)) {
            }
        } // end foreach status_effect
    }

    /**
     * populate name and slug data
     *
     * @param  array  $info
     *
     * @return [type]       [description]
     */
    public function createAllNames(array $info)
    {
        $info['name'] = $this->prettify(trim($info['raw_name'])); // add spaces to make pretty
        $info['slug'] = Str::slug(trim($info['name']));
        $info['raw_slug'] = Str::slug(trim($info['raw_name']));
        $info['true_slug'] = Str::slug(trim($info['true_name'])) ?? null;

        return $info;
    }

    /**
    *
    * @return \Illuminate\Http\Response
    */
    public function statusEffect()
    {
        echo "CONVERT status_effect";

        $status_effects_file = 'G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\status-effects\status-effect-list.json';

        $this->convertJsonList($status_effects_file, StatusEffect::class, ['raw_name']);
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
                // make sure we don't already have this item attached
                $matching_items = Item::where('name', $shared_data_info['name'])->get();
                $existing_items = $shared_data->items ?? null;
                // dd($shared_data_info['name'], $matching_items, $existing_items);
                if (isset($existing_items)) {
                    // items to attach
                    $items = $matching_items->diff($existing_items) ?? null;
                }

                // we don't want to attach unless it isn't already
                if (isset($items)) {
                    $items->each(function ($item, $key) use ($shared_data) {
                        $item->sharedData()->associate($shared_data);
                        $item->save();
                    });
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

                    // dd($shared_data_info['name'], $matching_items, $existing_items);
                    if ($status_effect->wasRecentlyCreated) {
                        // attach to item
                        $shared_data->setStatusEffect()->associate($status_effect);
                        $shared_data->save();
                    }

                    // attach to data
                    // make sure we don't already have this effect attached
                    $matching_status_effect = StatusEffect::where('name', $status_effect_name)->first();
                    $existing_item = $shared_data->item;
                    // dump($recipe_info['slug'], $item, $existing_item);
                    if (isset($existing_item)) {
                        // item to attach
                        $item = $existing_item->getKey() === $item->getKey() ? null : $item;
                    }
                }



                // we don't want to attach unless it isn't already
                if (isset($status_effect)) {
                    $shared_data->setStatusEffect()->associate($status_effect);
                    $shared_data->save();
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

            // attach to item
            // make sure we don't already have this item attached
            $item = Item::where('slug', $recipe_info['slug'])->first();
            $existing_item = $recipe->item;
            // dump($recipe_info['slug'], $item, $existing_item);
            if (isset($existing_item)) {
                // item to attach
                $item = $existing_item->getKey() === $item->getKey() ? null : $item;
            }

            if (isset($item)) {
                $item->recipe()->associate($recipe);
                $item->save();
            }

            if (!empty($recipe_info['resources'])) {
                // make sure we don't already have this requirement attached
                $existing_requirements = $recipe->requirements;
                $recipe_info['resources'] = collect($recipe_info['resources'])->filter(function ($value, $key) use ($existing_requirements) {
                    $existing = collect($existing_requirements->toArray());
                    // if even one part doesn't match, it's a new requirement
                    // Eloquent collection uses contains() differently, so set to regular collection
                    return (!$existing->contains('name', $value['name']) || !$existing->contains('amount', $value['amount']) || !$existing->contains('amount_per_level', $value['amount_per_level']) || !$existing->contains('recover', $value['recover']));
                })->toArray();

                foreach ($recipe_info['resources'] as $requirement_info) {
                    $requirement_info['slug'] = Str::slug(Item::name_EN($requirement_info['name']));

                    $requirement = Requirement::updateOrCreate(
                        [
                            'name'=>$requirement_info['name'],
                            'amount'=>$requirement_info['amount'],
                            'amount_per_level'=>$requirement_info['amount_per_level'],
                            'recover'=>$requirement_info['recover'],
                        ],
                        $requirement_info
                    );

                    // attach requirement to recipe
                    $requirement->recipe()->attach($recipe);
                    $requirement->save();

                    // make sure we don't already have this item attached
                    $item = Item::where('slug', $requirement_info['slug'])->first();
                    $existing_item = $requirement->item;
                    if (isset($existing_item)) {
                        // $item = $existing_item->contains($item) ? null : $item;
                        $item = $existing_item->getKey() === $item->getKey() ? null : $item;
                    }
                    if (isset($item)) {
                        $requirement->item()->associate($item);
                        $requirement->save();
                    }
                } // end each requirement
            } // endif requirements
            // TODO: set crafting station
            // TODO: set repair station
        } // end foreach recipe
    }

    /**
    * Display a listing of the requirement.
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

    /**
     * decode a JSON file
     *
     * @param  [string] $filepath filepath of JSON file
     *
     * @return Object    Decoded JSON
     */
    public static function decodeJsonFile($filepath, bool $toArray=false)
    {
        return json_decode(file_get_contents($filepath), $toArray);
    }

    /**
     * convert class name from C# (remove prefix)
     * e.g., Recipe_ArmorBronzeChest -> ArmorBronzeChest
     * Then this naem can be used to find the item
     *
     * @param  string $name
     *
     * @return string       the trimmed name
     */
    public static function removeCsPrefix(string $name)
    {
        return (explode('_', $name)[1]) ?? $name;
    }

    /**
     * Convert a name to pretty name by replacing underscores
     * with spaces, then splitting string into array on
     * camel or Studly case and turning it into a space delimited string
     * e.g., Recipe_ArmorBronzeChest -> Recipe Armor Bronze Chest
     *
     * regex: https://stackoverflow.com/questions/7593969/regex-to-split-camelcase-or-titlecase-advanced/7599674#7599674
     *
     * @param  string $name
     *
     * @return string       the converted name
     */
    public static function prettify(string $name)
    {
        $name = Str::of(trim($name))->replace('_', ' ');
        $name = Str::of($name)->split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/')->toArray();
        return implode(' ', $name) ?? $name;
    }
}
