<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function status_effect()
    {
        echo "CONVERT status_effect";

        $status_effects = json_decode(file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\status-effects\status-effect-list.json'), true);
        dump('STATUS EFFECTS', $status_effects);
        foreach ($status_effects as $status_effect_info) {
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
    public function recipe($name='')
    {
        echo "CONVERT RECIPE";
        if (!empty($name)) {
            return $this->convert($name, 'recipe');
        }

        $recipes = json_decode(file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\recipe-list.json'), true);
        // dump('RECIPES', $recipes);
        foreach ($recipes as $recipe_info) {
            $recipe = Recipe::updateOrCreate(
                ['name'=>$recipe_info['name']],
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
        $items = json_decode(
            file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\item-list.json'),
            true
        );
        dump('ITEMS');
        foreach ($items as $item_info) {
            dump($item_info['name']);

            $item = Item::updateOrCreate(
                ['name'=>$item_info['name']],
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
                    dump($shared_data_info);
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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        // convert all
        $this->status_effect();
        $this->item();
        $this->recipe();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tester()
    {
        $base_uri = 'https://valheim.fandom.com';
        // $uri_path = '/wiki/Category:Food/';
        $uri_path = '/api.php';
        // ?action=parse&page=Lox meat pie&format=json
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=Food
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages&section=1
        $uri = $base_uri.$uri_path;
        // $uri = 'https://google.com';

        // TODO: store values in database and only make HTTP request periodically
        /*Http::get($uri, [
            'action' => 'parse',
            'page' => "sausages",
            'format'=> 'json'
        ]);*/

        // fake response out for now
        // same as: https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages
        $response = json_decode(file_get_contents(storage_path('app/sausages.json')))->parse;

        // dump($response);

        $title = $response->displaytitle;
        dump($title);
        $images = $response->images;
        dump($images);
        $sections = $response->sections;
        dump($sections);
        $text = stripslashes(reset($response->text));
        // dump($text);

        // https://github.com/paquettg/php-html-parser
        $dom = new Dom;
        $dom->loadStr($text);
        $labelnodes = $dom->find('.pi-data-label');
        $valuenodes = $dom->find('.pi-data-value');

        $info = array_map(function ($label, $value) {
            // dump('label: '.$label->text."\nvalue: ".(empty(trim($value->text)) ? $value->getChildren()[0]->text : $value->text));

            if (empty(trim($value->text))) {
                $val = $value->getChildren()[0];
                if ($value->getChildren()[0]->tag->name() === 'a') {
                    return [
                        'label'=>$label->text,
                        'val'=>[
                            'link' => $val->tag->getAttribute('href')->getValue(),
                            'text' => $val->text
                        ]
                    ];
                }
                return ['label'=>$label->text,'val'=>['text' => $val->text]];
            }

            return ['label'=>$label->text, 'val'=>['text'=>$value->text]];
        }, $labelnodes->toArray(), $valuenodes->toArray());

        dump($info);
    }
}
