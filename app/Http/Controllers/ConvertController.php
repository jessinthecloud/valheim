<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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
    private $docspath = 'G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs';
    /**
    *
    * @return \Illuminate\Http\Response
    */
    public function craftingStation()
    {
        echo "CONVERT CRAFTING STATIONS";

        $crafting_stations_file = $this->docspath.'\crafting-station-list.json';

        $this->convertJsonList($crafting_stations_file, CraftingStation::class, ['raw_name']);
    }

    /**
    *
    * @return \Illuminate\Http\Response
    */
    public function statusEffect()
    {
        echo "CONVERT status_effect";

        $status_effects_file = $this->docspath.'\status-effect-list.json';

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

        $file = $this->docspath.'\item-list.json';
        $contents = $this->removeInvalidHex(file_get_contents($file));
        $items = json_decode($contents, true);
        // $this->convertJsonList($file, Item::class, ['slug'], ['shared_data']);

        foreach ($items as $item_info) {
            $item_info = $this->createAllNames($item_info);

            $item = Item::updateOrCreate(
                ['slug'=>$item_info['slug']],
                $item_info
            );

            $shared_data_info = $item_info['shared_data'];
            if (!empty($shared_data_info)) {
                /*dump((float)$shared_data_info['item_type']);
                // php is being very difficult about getting this value
                $shared_data_info['item_type'] = (new \ReflectionClass(ItemType::class))->getConstant(strtoupper($shared_data_info['item_type']));*/
                $shared_data_info = $this->createAllNames($shared_data_info);

                $shared_data = SharedData::updateOrCreate(
                    [
                        'raw_name' => $shared_data_info['raw_name'],
                        'item_type' => $shared_data_info['item_type'],
                    ],
                    $shared_data_info
                );

                // attach data to item
                // make sure we don't already have this item attached
                $existing_items = $shared_data->items ?? null;

                if (!empty($existing_items->toArray())) {
                    // items to attach
                    $item = collect()->add($item)->diff($existing_items) ?? null;
                    $item = $item[0] ?? null;
                }

                // we don't want to attach unless it isn't already
                if (isset($item)) {
                    $item->sharedData()->associate($shared_data);
                    $item->save();
                }

                // get status effects shared data
                if (!empty($shared_data_info['status_effects'])) {
                    $status_effects = $shared_data_info['status_effects'];

                    foreach ($status_effects as $status_effect_info) {
                        $type = $status_effect_info['type'];
                        // don't want to insert type
                        unset($status_effect_info['type']);

                        $status_effect_info = $this->createAllNames($status_effect_info);

                        $status_effect = StatusEffect::updateOrCreate(
                            ['slug' => $status_effect_info['slug']],
                            $status_effect_info
                        );
                        $matching_status_effect = StatusEffect::where('slug', $status_effect_info['slug'])->first();
                        $existing_status_effect = $shared_data->{$type.'StatusEffect'};

                        // attach to data
                        // make sure we don't already have this effect attached
                        if (isset($existing_status_effect)) {
                            // effect to attach
                            $status_effect = $existing_status_effect->getKey() === $status_effect->getKey() ? null : $status_effect;
                        }
                    }

                    if (isset($status_effect)) {
                        // attach to data
                        $shared_data->{$type.'StatusEffect'}()->associate($status_effect);
                        $shared_data->save();
                    }
                } // if not empty status effects

                // TODO: set damages
                // TODO: set damages_per_level
                //
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

        $file = $this->docspath.'\recipe-list.json';
        $contents = $this->removeInvalidHex(file_get_contents($file));
        $recipes = json_decode($contents, true);
        // $this->convertJsonList($file, Recipe::class, ['true_name'], ['item'?]);

        foreach ($recipes as $recipe_info) {
            $recipe_info = $this->createAllNames($recipe_info);

            $recipe = Recipe::updateOrCreate(
                [
                    'true_name'=>$recipe_info['true_name']
                ],
                $recipe_info
            );

            // attach to item
            // make sure we don't already have this item attached
            $item = Item::where('slug', $recipe_info['slug'])->first();
            $existing_item = $recipe->item;

            if (isset($existing_item) && isset($item)) {
                // item to attach
                $item = $existing_item->getKey() === $item->getKey() ? null : $item;
            }

            if (isset($item)) {
                $item->recipe()->associate($recipe);
                $item->save();
            }

            // create / attach recipe requirements
            if (!empty($recipe_info['requirements'])) {
                foreach ($recipe_info['requirements'] as $requirement_info) {
                    $requirement_info = $this->createAllNames($requirement_info);

                    $requirement = Requirement::updateOrCreate(
                        [
                            'raw_name'=>$requirement_info['raw_name'],
                            'amount'=>$requirement_info['amount'],
                            'amount_per_level'=>$requirement_info['amount_per_level'],
                            'recover'=>$requirement_info['recover'],
                        ],
                        $requirement_info
                    );

                    // attach requirement to recipe
                    $existing_requirements = $recipe->requirements ?? null;

                    if (!empty($existing_requirements->toArray())) {
                        // items to attach
                        $requirement = collect()->add($requirement)->diff($existing_requirements) ?? null;
                        $requirement = $requirement[0] ?? null;
                    }

                    // we don't want to attach unless it isn't already
                    if (isset($requirement)) {
                        $requirement->recipe()->attach($recipe);
                        $requirement->save();

                        // attach item to requirement
                        // make sure we don't already have this item attached
                        $item = Item::where('slug', $requirement->slug)->first();
                        $existing_item = $requirement->item;
                        if (isset($existing_item)) {
                            $item = $existing_item->getKey() === $item->getKey() ? null : $item;
                        }
                        if (isset($item)) {
                            $requirement->item()->associate($item);
                            $requirement->save();
                        }
                    }
                } // end each requirement
            } // endif requirements

            // attach to crafting station
            if (isset($recipe_info['raw_crafting_station_name'])) {
                // make sure we don't already have this station attached
                $station = CraftingStation::where('raw_name', $recipe_info['raw_crafting_station_name'])->first();
                $existing_station = $recipe->station;

                if (isset($existing_station)) {
                    // station to attach
                    $station = $existing_station->getKey() === $station->getKey() ? null : $station;
                }

                if (isset($station)) {
                    $recipe->craftingStation()->associate($station);
                    $recipe->save();
                }
            }

            // attach to repair station
            if (isset($recipe_info['raw_repair_station_name'])) {
                // make sure we don't already have this station attached
                $station = CraftingStation::where('raw_name', $recipe_info['raw_repair_station_name'])->first();
                $existing_station = $recipe->station;

                if (isset($existing_station)) {
                    // station to attach
                    $station = $existing_station->getKey() === $station->getKey() ? null : $station;
                }

                if (isset($station)) {
                    $recipe->repairStation()->associate($station);
                    $recipe->save();
                }
            }
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
        $this->craftingStation();
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
        $entities = json_decode($contents, true);
        // dump($entities);

        foreach ($entities as $entity_info) {
            $this->convertEntity($entity_info, $class, $unique_keys, $related_keys);
        } // end foreach entity
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
        // if strange case where only true name exists
        // OR if it is a recipe alt, use true name as raw name
        // e.g., Recipe_Adze or Bronze5
        if (!empty($info['true_name'])) {
            if (empty($info['raw_name']) || preg_match('/[0-9]/', $info['true_name'])) {
                $info['raw_name'] = $this->removeCsPrefix($info['true_name']);
            }
        }

        // add spaces to make pretty
        $info['name'] = $this->prettify(trim($info['raw_name']));
        $info['slug'] = Str::slug(trim($info['name']));
        $info['raw_slug'] = Str::slug(trim($info['raw_name']));
        $info['true_slug'] = isset($info['true_name']) ? Str::slug(trim($info['true_name'])) : null;

        return $info;
    }

    /**
     * update or create entities from JSON
     *
     * @param  [type] $entity_info  [description]
     * @param  [type] $class        [description]
     * @param  [type] $unique_keys  [description]
     * @param  [type] $related_keys [description]
     *
     * @return [type]               [description]
     */
    public function convertEntity($entity_info, $class, $unique_keys, $related_keys)
    {
        $entity_info = $this->createAllNames($entity_info);
        // dump("DATA FOR $class", $entity_info);
        // setup info for updateOrCreate()
        $check_columns = [];
        foreach ($unique_keys as $keyname) {
            $check_columns [$keyname]= $entity_info[$keyname];
        }
        $entity = $class::updateOrCreate(
            $check_columns,
            $entity_info
        );
        // dump($entity);
        /*// check for related Models
        if (!empty(array_filter($related_keys))) {
            // TODO: refactor this
            // Item shared data
            if (Arr::exists($related_keys, 'shared_data')) {
                $shared_data_info = $entity_info['shared_data'];
                if (!empty($shared_data_info)) {
                    // php is being very difficult about getting this value
                    $shared_data_info['item_type'] = (new \ReflectionClass(ItemType::class))->getConstant(strtoupper($shared_data_info['item_type']));
                    $this->convertItemSharedData($shared_data_info, $class);
                } // endif empty shared data
            } // end if shared_data related
        }*/
    }

    /*public function convertItemSharedData($shared_data_info, $class)
    {
        $this->convertEntity($shared_data_info, $class, ['raw_name', 'item_type'], ['status_effects']);


    }*/
}
