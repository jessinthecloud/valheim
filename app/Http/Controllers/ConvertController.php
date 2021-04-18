<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\JsonAdapter;
use App\Http\Controllers\RecipeController;
use App\Models\Recipe;

class ConvertController extends Controller
{
    /**
     * convert an item by unqiue name
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function convert($name)
    {
        dump("Convert: $name");
        $contents = JsonAdapter::decodeJsonFile(
            storage_path('app\recipes.json'),
            true
        );
        foreach ($contents as $k => $content) {
            // dump($content);
            $content = array_filter($content, function ($val, $key) use ($name) {
                // dump("$key === name");
                if (is_string($val) && $key === 'name' && $val === $name) {
                    return true;
                }

                return false;
            }, ARRAY_FILTER_USE_BOTH);
            // dump($content);
            if (!empty($content)) {
                $contents = $contents[$k];
                break;
            }
            // dd("dying");
        }
        dump($contents);
        $recipe = JsonAdapter::createObject($name, $contents);
        // dd($recipes);
        if (Recipe::where('name', $contents['name'])->first()) {
            dump("Recipe {$contents['name']} already exists.");
            dump($recipe);
        } else {
            $saved = $recipe->save();
            dump("save: ".$saved);
        }
    } // end func convert()

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
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
    public function tester()
    {
        $response = json_decode(file_get_contents(storage_path('app\itemdrops.json')));
        return $response;
        /*dump($response[1]);

        $array = json_decode(file_get_contents(storage_path('app\itemdrops.json')), true);

        $unique_properties = [];
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($array),
            \RecursiveIteratorIterator::SELF_FIRST
        )
          as $key => $value) {
            if (is_string($key)) {
                $unique_properties[]=$key;
            }
        }
        $unique_properties = array_unique($unique_properties);
        foreach ($unique_properties as $prop) {
            echo "$prop <BR>";
        }*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oldIndex()
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
