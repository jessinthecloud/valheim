<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConvertController extends Controller
{
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

        /*echo "<BR>";
        dump("////////////////////// BEFORE FUNC ////////////////////////////////");
        echo "<BR>";*/
        /////////////////

        // !!!!
        // $contents['recipes'] = array_slice($contents['recipes'], 0, 2);
        // !!!!
        // dd($contents);
        // $recipe = JsonAdapter::recursiveCreateFromArray($contents);
        $recipes = JsonAdapter::createFromArray($contents);
        /*echo "<BR>";
        dump("/////////////////////// AFTER FUNC ///////////////////////////////");
        echo "<BR>";*/
        // dump($recipes);
        foreach ($recipes as $recipe) {
            dump($recipe);
            dump($recipe->name);
            dd($recipe->getAttributes());
            $recipe->save();
        }
        /*$iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($contents),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $i=0;
        foreach ($iterator as $key => $item) {
            if ($i > 22) {
                break;
            }
            dump("key: $key");
            dump($item);
            dump("=$i===========================================$i=");
            $i++;
        }*/
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
