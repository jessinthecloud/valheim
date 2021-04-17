<?php

namespace App\Http\Controllers;

use PHPHtmlParser\Dom;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*dump($this->decodeJsonFromFile(
            storage_path('app\recipes.json')
        ));*/

        $recipe = Recipe::createFromJson($this->decodeJsonFromFile(
            storage_path('app\recipes.json'),
            true
        )[0]);

        echo "<BR>//////////////////////////////////////////////////////";

        dump($recipe);
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



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}

/**
* PHP's DOM classes are recursive but don't provide an implementation of
* RecursiveIterator. This class provides a RecursiveIterator for looping over DOMNodeList
*/
class DOMNodeRecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function __construct(\DOMNodeList $node_list)
    {
        $nodes = array();
        foreach ($node_list as $node) {
            $nodes[] = $node;
        }

        parent::__construct($nodes);
    }

    public function getRecursiveIterator()
    {
        return new \RecursiveIteratorIterator($this, \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }


    public function getChildren()
    {
        return new self($this->current()->childNodes);
    }
}
