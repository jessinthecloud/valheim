<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JsonAdapter;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ItemController;
use App\Models\Recipe;
use App\Models\Item;

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
        $contents = JsonAdapter::decodeJsonFile(
            storage_path('app\\'.strtolower($type).'s.json'),
            true
        );
        foreach ($contents as $k => $content) {
            // dump($content);
            $content = array_filter($content, function ($val, $key) use ($name) {
                // dump("$key === name");
                return (is_string($val) && $key === 'name' && $val === $name);
            }, ARRAY_FILTER_USE_BOTH);
            // dump($content);
            if (!empty($content)) {
                $contents = $contents[$k];
                break;
            }
            // dd("dying");
        }
        dump($contents);
        $item = JsonAdapter::createObject($name, $contents);
        // dd($recipes);
        if (ucfirst($type)::where('name', $contents['name'])->first()) {
            dump(ucfirst($type)." {$contents['name']} already exists.");
            dump($item);
        } else {
            $saved = $item->save();
            dump("save: ".$saved);
        }
    } // end func convert()

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function recipe($name='')
    {
        echo "CONVERT RECIPE";
        if (!empty($name)) {
            return $this->convert($name, 'recipe');
        }

        // decode json file to array
        $contents = ['recipes'=>JsonAdapter::decodeJsonFile(
            storage_path('app\recipes.json'),
            true
        )];

        $recipes = JsonAdapter::createFromArray($contents);

        // dump($recipes);
        foreach ($recipes as $recipe) {
            // dump($recipe);
            // dump($recipe->name);
            // dump($recipe->getAttributes());
            if (Recipe::where('name', $recipe->name)->first()) {
                dump("Recipe {$recipe->name} already exists.");
            } else {
                $saved = $recipe->save();
                dump("save: ".$saved);
            }
        }
    }

    /**
    * Display a listing of the resource.
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
        $contents = ['items'=>JsonAdapter::decodeJsonFile(
            storage_path('app\itemdrops.json'),
            true
        )];

        $items = JsonAdapter::createFromArray($contents);

        // dump($items);
        foreach ($items as $item) {
            // dump($item);
            // dump($item->name);
            // dump($item->getAttributes());
            if (Item::where('name', $item->name)->first()) {
                dump("item {$item->name} already exists.");
            } else {
                $saved = $item->save();
                dump("save: ".$saved);
            }
        }
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
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
