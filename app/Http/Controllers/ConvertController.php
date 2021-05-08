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
    public function recipe($name='')
    {
        echo "CONVERT RECIPE";
        if (!empty($name)) {
            return $this->convert($name, 'recipe');
        }

        $json = json_decode(file_get_contents('G:\Steam\steamapps\common\Valheim\BepInEx\plugins\ValheimJsonExporter\Docs\conceptual\objects\recipe-list.json'), true);
        dump($json);
        foreach ($json as $recipe_info) {
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
        // dump($items);
        foreach ($items as $item_info) {
            $item = Item::updateOrCreate(
                ['name'=>$item_info['name']],
                $item_info
            );
            // TODO: set shared_data
            if (!empty($item_info['shared_data'])) {

                // php is being very difficult about getting this value
                $item_info['shared_data']['item_type'] = (new \ReflectionClass(ItemType::class))->getConstant(strtoupper($item_info['shared_data']['item_type']));

                $shared_data = SharedData::updateOrCreate(
                    [
                        'name' => $item_info['shared_data']['name'],
                        'item_type' => $item_info['shared_data']['item_type'],
                    ],
                    $item_info['shared_data']
                );
                // attach to item
                $item->sharedData()->associate($shared_data);
                $item->save();
                // TODO: status effect ids
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
